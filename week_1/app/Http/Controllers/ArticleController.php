<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleSaveRequest;
use App\Models\Article;
use Illuminate\Http\Request;
use Storage;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::orderBy('id', 'desc')->get();

        return view('articles.list', [
            'articles' => $articles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        {
            return view('articles.save', [
                'pageMode' => 'write',
                'article' => new Article()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store( $request)
    {
        $validated = $request->validated();
        $article = new Article();
        $article->user_id = 1;
        $article->title = $validated['title'];
        $article->body = $validated['body'];

        if($request->hasFile('img_1')) {
            $article->img_1 = $request->file('img_1')->store('article/' . date('Y/m/d'), 'public');
        }
        $article->save();

        return redirect()->route('articles.show', $article->id)->with('success', "{$article->id}번 게시물이 작성되었습니다.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        return view('articles.detail',[
            'article' => $article
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        return view('articles.save', [
            'pageMode' => 'edit',
            'article' => $article
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update( $request, Article $article)
    {
        $validated = $request->validated();

        $article->title = $validated['title'];
        $article->body = $validated['body'];

        if($request->hasFile('img_1')) {
            if($article->img_1) {
                Storage::disk('public')->delete($article->img_1);
            }
            
            $article->img_1 = $request->file('img_1')->store('article/' . date('Y/m/d'), 'public');
        }

        $article->save();

        return redirect()->route('articles.show', $article->id)->with('success', "{$article->id}번 게시물을 수정하였습니다.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $id = $article->id;

        if($article->img_1) {
            Storage::disk('public')->delete($article->img_1);
        }
        
        $article->delete();

        return redirect()->route('articles.index')->with('success', "{$id}번 게시물을 삭제하였습니다.");
    }
}
