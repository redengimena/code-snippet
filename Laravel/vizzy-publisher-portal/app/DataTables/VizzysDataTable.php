<?php

namespace App\DataTables;

use App\Models\Vizzy;
use Illuminate\Http\Request;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class VizzysDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query, Request $request)
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('podcast_id',function($model){
                return $model->podcast->title;
            })
            ->editColumn('created_at',function($model){
                return $model->created_at;
            })
            ->editColumn('updated_at',function($model){
                return $model->updated_at;
            })
            ->editColumn('status',function($model){
                return '<a class="btn btn-xs btn-rounded btn-outline-dark mr-3 ml-auto">'.$model->status_name.'</a>';
            })
            ->addColumn('owner', function($model){
                return $model->podcast->user ? $model->podcast->user->email : 'Admin';
            })
            ->addColumn('action', function($model){
                $href = route('curator', ['podcast' => $model->podcast, 'guid' => $model->episode_guid]);
                return '<a class="btn btn-primary shadow btn-xs sharp mr-1" href="'.$href.'"><i class="fa fa-pencil"></i></a>';
            })
            ->filter(function ($instance) use ($request) {
                if ($request->get('status')) {
                    $instance->where('status', $request->get('status'));
                }
                if (!empty($request->get('search'))) {
                    $instance->where(function($w) use($request){
                        $search = $request->get('search');
                        $w->orWhere('title', 'LIKE', "%$search%")
                        ->orWhereHas('podcast', function($query) use ($search) {
                            return $query->where('title', 'LIKE', "%$search%");
                        })
                        ->orWhereHas('podcast', function($query) use ($search) {
                            return $query->WhereHas('user', function($query) use ($search) {
                                return $query->where('email', 'LIKE', "%$search%");
                            });
                        });
                    });
                }
            })
            ->rawColumns(['status', 'action']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Vizzy $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Vizzy $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('vizzys')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->dom('Bfrtip')
                    ->orderBy(4);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('id'),
            Column::make('podcast_id')->title('Podcast'),
            Column::make('title'),
            Column::make('owner'),
            Column::make('created_at'),
            Column::make('updated_at'),
            Column::make('status'),
            Column::make('action'),
        ];
    }
}