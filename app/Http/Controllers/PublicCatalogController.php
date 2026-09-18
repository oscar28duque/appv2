<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicCatalogController extends Controller
{
    /**
     * Muestra la página principal con el catálogo de alquiler de equipos, noticias y cotizador.
     */
    public function index(Request $request): View
    {
        $category = $request->input('category');
        $search = $request->input('search');

        $itemsQuery = Item::with('media')
            ->where('status', 'disponible')
            ->latest();

        if ($category && in_array($category, Item::CATEGORIES)) {
            $itemsQuery->where('category', $category);
        }

        if ($search) {
            $itemsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $items = $itemsQuery->get();
        $categories = Item::CATEGORIES;

        // Noticias institucionales (Módulo de Noticias / Reto B)
        $news = News::with('media')->published()->latest()->take(3)->get();

        return view('index', compact('items', 'categories', 'news', 'category', 'search'));
    }

    /**
     * Ficha pública de un equipo disponible.
     */
    public function show(string $code): View
    {
        $item = Item::with('media')->where('code', $code)->firstOrFail();
        $relatedItems = Item::with('media')
            ->where('category', $item->category)
            ->where('id', '!=', $item->id)
            ->take(3)
            ->get();

        return view('items.show', compact('item', 'relatedItems'));
    }
}
