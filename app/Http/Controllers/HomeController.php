<?php

namespace App\Http\Controllers;

use App\Services\AgentService;
use App\Services\BannerService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $currentTag = $request->input('tag', '推荐');
        $code =  $request->input('code', null);
        $banners = BannerService::getOnlineBanners();
        $tags = ProductService::$TAGS;
        if (!in_array($currentTag, $tags)) {
            return redirect()->route('home', ['tag' => '推荐', 'code' => $code]);
        }
        $products = ProductService::search($currentTag);
        return Inertia::render('Index', [
            'banners' => $banners,
            'products' => $products,
            'tags' => $tags,
            'code' => $code,
            'currentTag' => $currentTag
        ]);
    }
    public function customer(Request $request){
        $code = $request->input('code', env('AGENT_CODE','S5u2'));
        $agent = AgentService::findByCode($code);
        return Inertia::render('Customer', [
            'qrcode' => $agent['qrcode'],
        ]);
    }
}
