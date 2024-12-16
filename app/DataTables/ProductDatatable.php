<?php

namespace App\DataTables;

use App\Models\ProductCategory;
use App\Models\Product;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

use App\Traits\DataTableTrait;

class ProductDataTable extends DataTable
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

            ->editColumn('productcategory.title', function($query) {
                return $query->ProductCategory->title ?? '-';
            })
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
            ->addColumn('price', function($price){             
                $price = getPriceFormat($price->price);
                return $price;
            })

            ->addColumn('action', function($product){
                $id = $product->id;
                return view('product.action',compact('product','id'))->render();
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
    public function query(Product $model)
    {
        $model = Product::query()->with('productcategory');
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
            ['data' => 'affiliate_link', 'name' => 'affiliate_link', 'title' => __('message.affiliate_link')],
            ['data' => 'price', 'name' => 'price', 'title' => __('message.price')],
            ['data' => 'productcategory.title', 'name' => 'productcategory.title', 'title' => __('message.productcategory'), 'orderable' => false],
            ['data' => 'featured', 'name' => 'featured', 'title' => __('message.featured')],
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
        return 'Product' . date('YmdHis');
    }
}
