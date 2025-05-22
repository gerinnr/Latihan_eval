<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MahasiswaController extends Controller
{
    public function index()
    {
        $response = Http::get('http://localhost:8080/mahasiswa');
        if ($response->successful()) {
            $mahasiswa = $response->json();
            return view('dataMahasiswa', ['mahasiswa' => $mahasiswa]);
        }
        return view('dataMahasiswa', ['mahasiswa' => [], 'error' => 'Gagal mengambil data dosen']);
    }

        public function create()
    {
        $response = Http::get('http://localhost:8080/mahasiswa'); // Sesuaikan endpoint API

        if ($response->successful()) {
            $mahasiswa = $response->json();
            return view('tambahMahasiswa', compact('mahasiswa'));
        }

        return view('tambahMahasiswa', ['mahasiswa' => [], 'error' => 'Gagal mengambil data mahasiswa'
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'  => 'required|string|max:255',
            'nim'  => 'required|string|max:20',
            'email' => 'required|email',
            'prodi' => 'required|string|max:100',
        ]);

        Http::post('http://localhost:8080/mahasiswa', $data);

        return redirect()->route('mahasiswa.index')->with('success', 'Dosen berhasil ditambahkan!');
    }

        public function edit($nim)
    {
        // Ambil data dosen berdasarkan NIDN
        $response = Http::get("http://localhost:8080/mahasiswa/{$nim}");

        // Periksa apakah berhasil
        if ($response->successful()) {
            $mahasiswa = $response->json();
            return view('editMahasiswa', compact('mahasiswa'));
        }

        return redirect()->route('mahasiswa.index')->withErrors(['msg' => 'Gagal mengambil data dosen']);
    }

    public function update(Request $request, $nim)
    {
        // Validasi form
        $request->validate([
            'nama' => 'required|string|max:255',
            'nim' => 'required|numeric',
            'email' => 'required|email',
            'prodi' => 'required|string|max:255',
        ]);

        // Kirim data ke backend API
        $response = Http::put("http://localhost:8080/mahasiswa/{$nim}", [
            'nama' => $request->nama,
            'nim' => $request->nim,
            'email' => $request->email,
            'prodi' => $request->prodi,
        ]);

        // Cek hasil update
        if ($response->successful()) {
            return redirect()->route('mahasiswa.index')->with('success', 'Data dosen berhasil diperbarui');
        }

        // Jika gagal
        return back()->withErrors(['msg' => 'Gagal memperbarui data dosen'])->withInput();
    }

    public function destroy($nim)
    {
        Http::delete("http://localhost:8080/mahasiswa/$nim");

        return redirect()->route('mahasiswa.index')->with('success', 'Dosen berhasil dihapus.');
    }
}
