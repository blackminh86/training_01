<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Template as Template;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SliderRequest as MainRequest;
use App\Models\SliderModel as MainModel;

class SliderController extends Controller
{
    private $pathViewController = 'admin.pages.slider.';  // slider
    private $controllerName     = 'slider';
    private $params             = [];
    private $model;
    public function __construct()
    {
        $this->model = new MainModel();
        $this->params["pagination"]["totalItemsPerPage"] = 5;
        view()->share('controllerName', $this->controllerName);
    }
    public function index(Request $request)
    {
        $this->params['filter']['status'] = $request->input('filter_status', 'all');
        $this->params['search']['field']  = $request->input('search_field', ''); // all id description
        $this->params['search']['value']  = $request->input('search_value', '');

        $items              = $this->model->listItems($this->params, ['task'  => 'admin-list-items']);
        $itemsStatusCount   = $this->model->countItems($this->params, ['task' => 'admin-count-items-group-by-status']); // [ ['status', 'count']]
        return view($this->pathViewController .  'index', [
            'params'        => $this->params,
            'items'         => $items,
            'itemsStatusCount' =>  $itemsStatusCount
        ]);
    }
    public function status(Request $request)
    {
        $this->params['status'] = $request->status;
        $this->params['id'] = $request->id;
        $this->params['search']['value'] = '';
        $this->params['search']['field'] = 'all';
        $this->model->saveItem($this->params, ['task' => 'change-status']);
        $itemsStatusCount   = $this->model->countItems($this->params, ['task' => 'admin-count-items-group-by-status']);
        $status = $request->status == 'active' ? 'inactive' : 'active';
        $link = route($this->controllerName . '/status', ['status' => $status, 'id' => $request->id]);
        return response()->json([
            'statusObj' => config('zvn.template.status')[$status],
            'filter' => Template::showButtonFilter($this->controllerName, $itemsStatusCount, 'all', $this->params['search']),
            'link' => $link,
        ]);
    }
    public function delete(Request $request)
    {
        $this->params['id'] = $request->id;
        $this->model->delete($this->params, ['task' => 'delete-item']);
        return redirect(route($this->controllerName));
    }
    public function form(Request $request)
    {
        $item = null;
        if ($request->id !== null) {
            $this->params['id'] = $request->id;
            $item = $this->model->getItem($this->params, ['task' => 'get-item']);
        }
        return view($this->pathViewController .  'form', [
            'item' => $item
        ]);
    }
    public function save(Request $request)
    {
        dd('Hello Save');
        if ($request->method() === 'POST') {
            //dd('Hello Save');
            $param = $request->all();
            dd($param);
        }
    }
}
