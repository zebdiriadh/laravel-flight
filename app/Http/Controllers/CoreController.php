<?php
// app/Http/Controllers/BaseController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\Controller;

class CoreController extends Controller
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // CRUD methods
    public function index()
    {
        $data = $this->model->all();
        return view('index', compact('data'));
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $request)
    {
        $this->model->create($request->all());
        return redirect()->route('index');
    }

    public function edit($id)
    {
        $item = $this->model->findOrFail($id);
        return view('edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = $this->model->findOrFail($id);
        $item->update($request->all());
        return redirect()->route('index');
    }

    public function destroy($id)
    {
        $this->model->findOrFail($id)->delete();
        return redirect()->route('index');
    }
}
