<?php

namespace App\DataTables;

use App\Models\Exercise;
use App\Models\Equipment;
use App\Models\Level;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

use App\Traits\DataTableTrait;

class ExerciseDataTable extends DataTable
{
    use DataTableTrait;
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)

            ->editColumn('status', function($query) {
                $status = 'warning';
                switch ($query->status) {
                    case 'active':
                        $status = 'primary';
                        break;
                    case 'inactive':
                        $status = 'warning';
                        break;
                }
                return '<span class="text-capitalize badge bg-'.$status.'">'.$query->status.'</span>';
            })
            ->editColumn('equipment.title', function($query) {
                return optional($query->equipment)->title ?? '-';
            })
            ->filterColumn('equipment.title', function($query, $keyword) {
                return $query->orWhereHas('equipment', function($q) use($keyword) {
                    $q->where('title', 'like', "%{$keyword}%");
                });
            })

            ->editColumn('level.title', function($query) {
                return optional($query->level)->title ?? '-';
            })
            ->filterColumn('level.title', function($query, $keyword) {
                return $query->orWhereHas('level', function($q) use($keyword) {
                    $q->where('title', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('action', function($exercise){
                $id = $exercise->id;
                return view('exercise.action',compact('exercise','id'))->render();
            })
            ->addIndexColumn()
            ->rawColumns(['action','status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Exercise $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Exercise $model)
    {
        $model = Exercise::query()->with('equipment','level');
        return $this->applyScopes($model);
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('message.srno'))
                ->orderable(false),
            ['data' => 'title', 'name' => 'title', 'title' => __('message.title')],   
            ['data' => 'equipment.title', 'name' => 'equipment.title', 'title' => __('message.equipment'), 'orderable' => false], 
            ['data' => 'level.title', 'name' => 'level.title', 'title' => __('message.level'), 'orderable' => false],   
            ['data' => 'status', 'name' => 'status', 'title' => __('message.status')],
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->title(__('message.action'))
                  ->width(60)
                  ->addClass('text-center hide-search'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Exercise' . date('YmdHis');
    }
}
