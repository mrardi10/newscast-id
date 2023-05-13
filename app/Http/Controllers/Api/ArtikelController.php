<?php

namespace App\Http\Controllers\Api;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArtikelResource;
use Cviebrock\EloquentSluggable\Services\SlugService;
use App\Models\Artikel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ArtikelController extends Controller
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
        //get artikels
        $artikels = Artikel::latest()->paginate(5);

        //return collection of posts as a resource
        return new ArtikelResource(true, 'List Data Artikel', $artikels);
    }

    public function store(Request $request)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'cover'     => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'judul'     => 'required',
            'deskripsi'   => 'required',
            'kategori_berita'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('cover');
        $image->storeAs('public/artikel', $image->hashName());

        //create post
        $post = Artikel::create([
            'cover'     => $image->hashName(),
            'judul'     => $request->judul,
            'kategori_berita'     => $request->kategori_berita,
            'slug' => SlugService::createSlug(Artikel::class, 'slug', $request->judul),
            'deskripsi'   => $request->deskripsi,
            'tag'   => $request->tag,
        ]);

        //return response
        return new ArtikelResource(true, 'Data Artikel Berhasil Ditambahkan!', $post);
    }

    public function show(Artikel $artikel)
    {
        //return single post as a resource
        return new ArtikelResource(true, 'Data Artikel Ditemukan!', $artikel);
    }

    public function update(Request $request, Artikel $artikel)
    {
        //define validation rules
        $validator = Validator::make($request->all(), [
            'judul'     => 'required',
            'deskripsi'   => 'required',
            'kategori_berita'   => 'required',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check if image is not empty
        if ($request->hasFile('cover')) {

            //upload image
            $image = $request->file('cover');
            $image->storeAs('public/artikel', $image->hashName());

            //delete old image
            Storage::delete('public/artikel/' . $artikel->cover);

            //update post with new image
            $artikel->update([
                'cover'     => $image->hashName(),
                'judul'     => $request->judul,
                'kategori_berita'     => $request->kategori_berita,
                'slug' => SlugService::createSlug(Artikel::class, 'slug', $request->judul),
                'deskripsi'   => $request->deskripsi,
                'tag'   => $request->tag,
            ]);
        } else {

            //update post without image
            $artikel->update([
                'judul'     => $request->judul,
                'kategori_berita'     => $request->kategori_berita,
                'slug' => SlugService::createSlug(Artikel::class, 'slug', $request->judul),
                'deskripsi'   => $request->deskripsi,
                'tag'   => $request->tag,
            ]);
        }

        //return response
        return new ArtikelResource(true, 'Data Artikel Berhasil Diubah!', $artikel);
    }

    public function destroy(Artikel $artikel)
    {
        //delete image
        Storage::delete('public/artikel/' . $artikel->cover);

        //delete artikel
        $artikel->delete();

        //return response
        return new ArtikelResource(true, 'Data Post Berhasil Dihapus!', null);
    }
}