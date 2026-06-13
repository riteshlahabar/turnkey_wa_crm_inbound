<?php

namespace App\Http\Controllers\CRM\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class BaseMasterController extends Controller
{
    protected string $modelClass;
    protected string $viewFolder;
    protected string $routePrefix;
    protected string $title;
    protected array $extraFields = [];

    public function index()
    {
        $items = ($this->modelClass)::orderBy('sort_order')->orderBy('name')->paginate(20);
        return view("crm.settings.{$this->viewFolder}.index", [
            'items' => $items,
            'title' => $this->title,
            'routePrefix' => $this->routePrefix,
            'extraFields' => $this->extraFields,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];

        foreach ($this->extraFields as $field => $config) {
            $rules[$field] = $config['rule'];
        }

        $data = $request->validate($rules);
        $data['is_active'] = $request->boolean('is_active', true);

        ($this->modelClass)::create($data);

        return back()->with('success', $this->title . ' added successfully.');
    }

    public function update(Request $request, $id)
    {
        $item = ($this->modelClass)::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ];

        foreach ($this->extraFields as $field => $config) {
            $rules[$field] = $config['rule'];
        }

        $data = $request->validate($rules);
        $data['is_active'] = $request->boolean('is_active', false);

        $item->update($data);

        return back()->with('success', $this->title . ' updated successfully.');
    }

    public function destroy($id)
    {
        $item = ($this->modelClass)::findOrFail($id);
        $item->delete();

        return back()->with('success', $this->title . ' deleted successfully.');
    }
}
