<?php

namespace App\Http\Controllers\Api;

use App\Models\Kategori;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\KategoriResource;
use Illuminate\Support\Facades\Validator;

class KategoriController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get posts
        $kategori = Kategori::latest()->paginate(5);

        //return collection of posts as a resource
        return new KategoriResource(true, 'List Data Kategori', $kategori);
    }
    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'kode'     => 'required', #kiri key postman, required (harus diisi)
            'nama'   => 'required', #kiri key postman, required (harus diisi)
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create post
        $kategori = Kategori::create([
            'kode'     => $request->kode, #kiri ikutin database , kanan ikutin key postman
            'nama'   => $request->nama, #kiri ikutin database , kanan ikutin key postman
        ]);

        //return response
        return new KategoriResource(true, 'Data Kategori Berhasil Ditambahkan!', $kategori);
    }
    public function show(Kategori $kategori)
    {
        //return single post as a resource
        return new KategoriResource(true, 'Data Kategori Ditemukan!', $kategori);
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $post
     * @return void
     */
    public function update(Request $request, Kategori $kategori)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'kode'     => 'required',
            'nama'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update kategori
        $kategori->update([
            'kode'     => $request->kode,
            'nama'     => $request->nama
        ]);

        //return response
        return new KategoriResource(true, 'Data Kategori Berhasil Diubah!', $kategori);
    }

    public function destroy(Kategori $kategori)
    {

        //delete kategori
        $kategori->delete();

        //return response
        return new KategoriResource(true, 'Data Kategeori Berhasil Dihapus!', null);
    }
}