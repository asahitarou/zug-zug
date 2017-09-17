<?php

namespace App\Http\Sections;

use AdminDisplay;
use AdminForm;
use AdminFormElement;
use App\Models\Category;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Section;

/**
 * Class Categories
 *
 * @property Category $model
 *
 * @see http://sleepingowladmin.ru/docs/model_configuration_section
 */
class Categories extends Section implements Initializable
{
    /**
     * @var \App\Models\Category
     */
    protected $model = '\App\Models\Category';

    /**
     * Initialize class.
     */
    public function initialize()
    {
        // Добавление пункта меню и счетчика кол-ва записей в разделе
        $this->addToNavigation($priority = 500, function () {
            return Category::count();
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
    protected $title = 'Категории товаров';

    /**
     * URL по которому будет доступен раздел
     * @var string
     */
    protected $alias = 'categories';

    /**
     * @return Первичная отображаемая таблица
     */
    public function onDisplay()
    {
        return AdminDisplay::tree()->setValue('title')
            ->setHtmlAttribute('class', 'table-primary');
    }

    /**
     * @param int $id
     * @return FormInterface
     */
    public function onEdit($id)
    {
        return AdminForm::panel()->addBody([
            AdminFormElement::text('title', 'Название категории')->required(),
            AdminFormElement::checkbox('is_active', 'Отображение'),
            AdminFormElement::text('created_at')->setLabel('Создано')->setReadonly(1),
            AdminFormElement::text('updated_at')->setLabel('Изменено')->setReadonly(1),

        ]);
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
        return 'Добавить категорию';
    }

    // иконка для пункта меню - шестеренка
    public function getIcon()
    {
        return 'fa fa-gear';
    }
}
