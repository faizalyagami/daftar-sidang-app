<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReviewerAssignment;
use App\Models\PendaftaranSkripsi;
use App\Models\PendaftaranMetodologi;
use Illuminate\Support\Facades\DB;

class ReviewerAssignmentService
{
    /**
     * Get all active reviewers
     */
    public function getActiveReviewers()
    {
        return User::whereHas('role', function($q) {
            $q->where('role', 'reviewer');
        })->where('is_active', true)->get();
    }

    /**
     * Get reviewer with least assignments
     */
    public function getReviewerWithLeastAssignments()
    {
        $reviewers = $this->getActiveReviewers();
        
        if ($reviewers->isEmpty()) {
            return null;
        }
        
        $reviewerLoad = [];
        
        foreach ($reviewers as $reviewer) {
            $pendingCount = ReviewerAssignment::where('reviewer_id', $reviewer->id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->count();
            
            $reviewerLoad[$reviewer->id] = [
                'reviewer' => $reviewer,
                'load' => $pendingCount
            ];
        }
        
        // Sort by load (ascending)
        usort($reviewerLoad, function($a, $b) {
            return $a['load'] - $b['load'];
        });
        
        return $reviewerLoad[0]['reviewer'] ?? null;
    }

    /**
     * Assign pendaftaran to reviewer automatically
     */
    public function autoAssign($pendaftaran, $type)
    {
        $reviewer = $this->getReviewerWithLeastAssignments();
        
        if (!$reviewer) {
            return [
                'success' => false,
                'message' => 'Tidak ada reviewer yang tersedia'
            ];
        }
        
        DB::beginTransaction();
        
        try {
            // Update pendaftaran
            $pendaftaran->update([
                'reviewer_id' => $reviewer->id,
                'assigned_at' => now(),
                'status' => 'review'
            ]);
            
            // Create assignment record
            ReviewerAssignment::create([
                'reviewer_id' => $reviewer->id,
                'pendaftaran_id' => $pendaftaran->id,
                'pendaftaran_type' => $type,
                'status' => 'pending',
                'assigned_at' => now()
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'reviewer' => $reviewer,
                'message' => "Pendaftaran diassign ke reviewer: {$reviewer->name}"
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Gagal mengassign: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Manual assign to specific reviewer
     */
    public function manualAssign($pendaftaran, $type, $reviewerId)
    {
        $reviewer = User::find($reviewerId);
        
        if (!$reviewer || !$reviewer->hasRole('reviewer')) {
            return [
                'success' => false,
                'message' => 'Reviewer tidak valid'
            ];
        }
        
        DB::beginTransaction();
        
        try {
            $pendaftaran->update([
                'reviewer_id' => $reviewer->id,
                'assigned_at' => now(),
                'status' => 'review'
            ]);
            
            ReviewerAssignment::create([
                'reviewer_id' => $reviewer->id,
                'pendaftaran_id' => $pendaftaran->id,
                'pendaftaran_type' => $type,
                'status' => 'pending',
                'assigned_at' => now()
            ]);
            
            DB::commit();
            
            return [
                'success' => true,
                'reviewer' => $reviewer,
                'message' => "Pendaftaran diassign ke reviewer: {$reviewer->name}"
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Gagal mengassign: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get reviewer statistics
     */
    public function getReviewerStats()
    {
        $reviewers = $this->getActiveReviewers();
        $stats = [];
        
        foreach ($reviewers as $reviewer) {
            $pending = ReviewerAssignment::where('reviewer_id', $reviewer->id)
                ->where('status', 'pending')
                ->count();
            
            $inProgress = ReviewerAssignment::where('reviewer_id', $reviewer->id)
                ->where('status', 'in_progress')
                ->count();
            
            $completed = ReviewerAssignment::where('reviewer_id', $reviewer->id)
                ->where('status', 'completed')
                ->count();
            
            $stats[] = [
                'reviewer' => $reviewer,
                'pending' => $pending,
                'in_progress' => $inProgress,
                'completed' => $completed,
                'total' => $pending + $inProgress + $completed
            ];
        }
        
        return $stats;
    }
}