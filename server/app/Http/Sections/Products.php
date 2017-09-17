<?php

namespace App\Http\Sections;

use AdminColumn;
use AdminColumnEditable;
use AdminDisplay;
use AdminForm;
use AdminFormElement;
use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Config;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Form\FormElements;
use SleepingOwl\Admin\Section;

/**
 * Class Products
 *
 * @property Product $model
 *
 * @see http://sleepingowladmin.ru/docs/model_configuration_section
 */
class Products extends Section implements Initializable
{
    /**
     * @var \App\Models\Product
     */
    protected $model = '\App\Models\Product';

    /**
     * Initialize class.
     */
    public function initialize()
    {
        // Добавление пункта меню и счетчика кол-ва записей в разделе
        $this->addToNavigation($priority = 500, function () {
            return Product::count();
        });
    }

    /**
     * @var bool
     */
    protected $checkAccess = false;

    /**
     * Заголовок раздела и название пункта в меню
     * @var string
     */
    protected $title = 'Товары';

    /**
     * URL по которому будет доступен раздел
     * @var string
     */
    protected $alias = 'products';

    /**
     * @return Первичная отображаемая таблица
     */
    public function onDisplay()
    {
        $display = AdminDisplay::datatablesAsync()->with('pictures', 'categories')
            ->setDisplaySearch(true)
            ->setHtmlAttribute('class', 'table-primary')
            ->setColumns(
                AdminColumn::link('title', 'Наименование товара')->setWidth('200px'),
                AdminColumn::text('description', 'Описание'),
                AdminColumn::lists('categories.title', 'Категории')
                    ->setHtmlAttribute('class', 'hidden-sm hidden-xs hidden-md'),
                AdminColumnEditable::checkbox('is_active', 'Видно', 'Не видно')->setLabel('Отображение'),
                AdminColumn::datetime('created_at', 'Создано')->setOrderable('created_at'),
                AdminColumn::datetime('updated_at', 'Изменено')->setOrderable('updated_at')
            )->paginate(20);

        return $display;
    }

    /**
     * @param int $id
     *
     * @return FormInterface
     */
    public function onEdit($id)
    {
        $form = AdminForm::panel();

        $form->addHeader([
            AdminFormElement::columns()
                ->addColumn([
                    AdminFormElement::text('title', 'Наименование товара')->required()
                ], 3)->addColumn([
                    AdminFormElement::number('price', 'Цена')->setMin(0)
                ], 3)->addColumn([
                    AdminFormElement::checkbox('is_active', 'Отображение')
                ]),
        ]);

        $tabs = AdminDisplay::tabbed([
            'Информация' => new FormElements([
                AdminFormElement::columns()
                    ->addColumn([
                        AdminFormElement::textarea('description', 'Описание')
                    ], 8)->addColumn([
                        AdminFormElement::textarea('attributes', 'Атрибуты')
                    ])
            ]),
            'Категории товара' => new FormElements([
                AdminFormElement::multiselect('categories', 'Категория', Category::class)
                    ->setDisplay('title')
                    ->setLoadOptionsQueryPreparer(function ($element, $query) {
                        return $query
                            ->where('is_active', true)
                            ->where('is_last_level', true);
                    })
            ]),
            'Картинки' => new FormElements([
                AdminFormElement::images('images', 'Добавить картинки')->storeAsJson()
                    ->setAfterSaveCallback(function ($value, $model) {
                        $value = array_map(function ($item) {
                            return str_replace(Config::get("app.images.uploadDirectory") . "/", '', $item);
                        }, $value);
                        $old = Image::where('product_id', '=', $model->id)->whereNotIn('name', $value)->get();
                        foreach ($old as $item) {
                            $item->delete();
                        }

                        $images = [];
                        foreach ($value as $path) {
                            $img = Image::firstOrCreate(['name' => $path], ['product_id' => $model->id]);
                            $images [] = Config::get("app.images.uploadDirectory") . "/" . $img->name;
                        }
                        $model->images = json_encode($images);
                        $model->save();
                    })
            ]),
        ]);
        $form->addElement($tabs);

        return $form;
    }

    /**
     * @return FormInterface
     */
    public function onCreate()
    {
        return $this->onEdit(null);
    }

    /**
     * @return void
     */
    public function onDelete($id)
    {
        // todo: remove if unused
    }

    /**
     * @return void
     */
    public function onRestore($id)
    {
        // todo: remove if unused
    }

    //заголовок для создания записи
    public function getCreateTitle()
    {
        return 'Создание товара';
    }

    // иконка для пункта меню - шестеренка
    public function getIcon()
    {
        return 'fa fa-gear';
    }
}
