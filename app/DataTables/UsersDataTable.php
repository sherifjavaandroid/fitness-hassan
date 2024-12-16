<?php

namespace App\DataTables;

use App\Models\User;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class UsersDataTable extends DataTable
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
            ->editColumn('userProfile.age', function($query) {
                return $query->userProfile->age ?? '-';
            })
            
            ->editColumn('status', function($query) {
                $status = 'warning';
                switch ($query->status) {
                    case 'active':
                        $status = 'primary';
                        break;
                    case 'inactive':
                        $status = 'danger';
                        break;
                    case 'banned':
                        $status = 'dark';
                        break;
                }
                return '<span class="text-capitalize badge bg-'.$status.'">'.$query->status.'</span>';
            })
            ->filterColumn('userProfile.age', function($query, $keyword) {
                return $query->orWhereHas('userProfile', function($q) use($keyword) {
                    $q->where('age', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('action', 'users.action')
            ->rawColumns(['action','status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\User $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query()
    {
        $model = User::whereNotIn('user_type', ['admin'])->with('userProfile');
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
            ['data' => 'id', 'name' => 'id', 'title' =>  __('message.id')],
            ['data' => 'display_name', 'name' => 'display_name', 'title' => __('message.name')],
            ['data' => 'phone_number', 'name' => 'phone_number', 'title' => __('message.phone_number')],
            ['data' => 'email', 'name' => 'email', 'title' => __('message.email')],
            ['data' => 'userProfile.age', 'name' => 'userProfile.age', 'title' => __('message.age'), 'orderable' => false],
            ['data' => 'status', 'name' => 'status', 'title' => __('message.status')], 

            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->title(__('message.action'))
                  ->searchable(false)
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
        return 'Users_' . date('YmdHis');
    }
}
