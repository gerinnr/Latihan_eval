<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;;
use Illuminate\Support\Facades\Http;
class DosenController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:8080/dosen');
        if ($response->successful()) {
            $dosen = $response->json();
            return view('dataDosen', ['dosen' => $dosen]);
        }
        return view('dataDosen', ['dosen' => [], 'error' => 'Gagal mengambil data dosen']);
    }

        public function create()
    {
        $response = Http::get('http://localhost:8080/dosen'); // Sesuaikan endpoint API

        if ($response->successful()) {
            $dosen = $response->json();
            return view('tambahDosen', compact('dosen'));
        }

        return view('tambahDosen', ['dosen' => [], 'error' => 'Gagal mengambil data mahasiswa'
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'  => 'required|string|max:255',
            'nidn'  => 'required|string|max:20',
            'email' => 'required|email',
            'prodi' => 'required|string|max:100',
        ]);

        Http::post('http://localhost:8080/dosen', $data);

        return redirect()->route('dosen.index')->with('success', 'Dosen berhasil ditambahkan!');
    }

        public function edit($nidn)
    {
        // Ambil data dosen berdasarkan NIDN
        $response = Http::get("http://localhost:8080/dosen/{$nidn}");

        // Periksa apakah berhasil
        if ($response->successful()) {
            $dosen = $response->json();
            return view('editDosen', compact('dosen'));
        }

        return redirect()->route('dosen.index')
            ->withErrors(['msg' => 'Gagal mengambil data dosen']);
    }

    public function update(Request $request, $nidn)
    {
        // Validasi form
        $request->validate([
            'nama' => 'required|string|max:255',
            'nidn' => 'required|numeric',
            'email' => 'required|email',
            'prodi' => 'required|string|max:255',
        ]);

        // Kirim data ke backend API
        $response = Http::put("http://localhost:8080/dosen/{$nidn}", [
            'nama' => $request->nama,
            'nidn' => $request->nidn,
            'email' => $request->email,
            'prodi' => $request->prodi,
        ]);

        // Cek hasil update
        if ($response->successful()) {
            return redirect()->route('dosen.index')->with('success', 'Data dosen berhasil diperbarui');
        }

        // Jika gagal
        return back()->withErrors(['msg' => 'Gagal memperbarui data dosen'])->withInput();
    }

    public function destroy($nidn)
    {
        Http::delete("http://localhost:8080/dosen/$nidn");

        return redirect()->route('dosen.index')->with('success', 'Dosen berhasil dihapus.');
    }
}