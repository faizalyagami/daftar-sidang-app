<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicPeriodController extends Controller
{
    public function index()
    {
        $periods = AcademicPeriod::orderBy('created_at', 'desc')->paginate(10);
        $activePeriod = AcademicPeriod::getActive();
        return view('admin.academic-periods.index', compact('periods', 'activePeriod'));
    }

    public function create()
    {
        return view('admin.academic-periods.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_akademik' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'skripsi_open' => 'boolean',
            'metodologi_open' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // If this period is set as active, deactivate others
        if ($request->has('is_active')) {
            AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicPeriod::create([
            'semester' => $request->semester,
            'tahun_akademik' => $request->tahun_akademik,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'skripsi_open' => $request->has('skripsi_open') ? 1 : 0,
            'metodologi_open' => $request->has('metodologi_open') ? 1 : 0,
        ]);

        return redirect()->route('admin.academic-periods.index')
            ->with('success', 'Periode akademik berhasil ditambahkan');
    }

    public function edit($id)
    {
        $period = AcademicPeriod::findOrFail($id);
        return view('admin.academic-periods.edit', compact('period'));
    }

    public function update(Request $request, $id)
    {
        $period = AcademicPeriod::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_akademik' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'skripsi_open' => 'boolean',
            'metodologi_open' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // If this period is set as active, deactivate others
        if ($request->has('is_active')) {
            AcademicPeriod::where('id', '!=', $id)->where('is_active', true)->update(['is_active' => false]);
        }

        $period->update([
            'semester' => $request->semester,
            'tahun_akademik' => $request->tahun_akademik,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'is_active' => $request->has('is_active') ? 1 : 0,
            'skripsi_open' => $request->has('skripsi_open') ? 1 : 0,
            'metodologi_open' => $request->has('metodologi_open') ? 1 : 0,
        ]);

        return redirect()->route('admin.academic-periods.index')
            ->with('success', 'Periode akademik berhasil diupdate');
    }

    public function destroy($id)
    {
        $period = AcademicPeriod::findOrFail($id);
        $period->delete();

        return redirect()->route('admin.academic-periods.index')
            ->with('success', 'Periode akademik berhasil dihapus');
    }

    public function setActive($id)
    {
        // Deactivate all
        AcademicPeriod::where('is_active', true)->update(['is_active' => false]);
        
        // Activate selected
        $period = AcademicPeriod::findOrFail($id);
        $period->update([
            'is_active' => true,
        ]);

        return redirect()->route('admin.academic-periods.index')
            ->with('success', 'Periode akademik aktif berhasil diubah');
    }
}