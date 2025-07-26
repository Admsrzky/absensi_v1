<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jabatan;
use Illuminate\Support\Facades\Redirect;

class JabatanController extends Controller
{
    /**
     * Display a listing of the job positions.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Jabatan::query();

        if ($request->has('search_jabatan') && !empty($request->search_jabatan)) {
            $query->where('jabatan', 'like', '%' . $request->search_jabatan . '%');
        }

        $jabatan = $query->orderBy('jabatan', 'asc')->paginate(10);

        return view('user.jabatan', compact('jabatan'));
    }

    /**
     * Store a newly created job position in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            // id_jabatan sekarang WAJIB diisi
            'id_jabatan' => 'required|string|max:255|unique:jabatan,id_jabatan',
            'jabatan' => 'required|string|max:255|unique:jabatan,jabatan',
        ], [
            'id_jabatan.required' => 'ID Jabatan harus diisi.', // Pesan error baru
            'id_jabatan.unique' => 'ID Jabatan ini sudah ada.',
            'jabatan.required' => 'Nama jabatan harus diisi.',
            'jabatan.unique' => 'Nama jabatan ini sudah ada.',
        ]);

        try {
            Jabatan::create([
                'id_jabatan' => $request->id_jabatan,
                'jabatan' => $request->jabatan,
            ]);
            return Redirect::back()->with('success', 'Data jabatan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Gagal menambahkan data jabatan: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified job position in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Jabatan  $jabatan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Jabatan $jabatan)
    {
        $request->validate([
            // id_jabatan sekarang WAJIB diisi, dan unik kecuali untuk record ini
            'id_jabatan' => 'required|string|max:255|unique:jabatan,id_jabatan,' . $jabatan->id . ',id',
            'jabatan' => 'required|string|max:255|unique:jabatan,jabatan,' . $jabatan->id . ',id',
        ], [
            'id_jabatan.required' => 'ID Jabatan harus diisi.', // Pesan error baru
            'id_jabatan.unique' => 'ID Jabatan ini sudah ada.',
            'jabatan.required' => 'Nama jabatan harus diisi.',
            'jabatan.unique' => 'Nama jabatan ini sudah ada.',
        ]);

        try {
            $jabatan->update([
                'id_jabatan' => $request->id_jabatan,
                'jabatan' => $request->jabatan,
            ]);
            return Redirect::back()->with('success', 'Data jabatan berhasil diperbarui!');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Gagal memperbarui data jabatan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified job position from storage.
     *
     * @param  \App\Models\Jabatan  $jabatan
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Jabatan $jabatan)
    {
        try {
            $jabatan->delete();
            return Redirect::back()->with('success', 'Data jabatan berhasil dihapus!');
        } catch (\Exception $e) {
            if ($e->getCode() == "23000") {
                return Redirect::back()->with('error', 'Gagal menghapus jabatan. Jabatan ini masih digunakan oleh data karyawan lain.');
            }
            return Redirect::back()->with('error', 'Gagal menghapus data jabatan: ' . $e->getMessage());
        }
    }
}
