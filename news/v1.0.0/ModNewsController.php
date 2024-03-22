<?php

require_once DOC_ROOT . 'core2/inc/classes/Common.php';
require_once DOC_ROOT . 'core2/inc/classes/class.list.php';
require_once DOC_ROOT . 'core2/inc/classes/class.edit.php';
require_once DOC_ROOT . 'core2/inc/classes/class.tab.php';
require_once DOC_ROOT . 'core2/inc/classes/Templater3.php';

/**
 * Class ModNewsController
 * @package Core2
 */


class ModNewsController extends \Common {
    private $app = "index.php?module=news";
    private $news;

    /**
     * Метод для отображения списка новостей
     */
    public function action_show_news() {
        $list = new \listTable('core_news');

        $list->SQL = "
        SELECT id,
             title,
             content,
             date
        FROM core_news AS news
        WHERE id > 0 /*ADD_SEARCH*/
        ORDER BY id 
        ";
        $list->addColumn($this->translate->tr("Заголовок"), "165", "TEXT");
        $list->addColumn($this->translate->tr("Контент"), "", "TEXT");
        $list->addColumn($this->translate->tr("Дата"), "75", "DATE");

        $list->addSearch($this->_("Идентификатор:"), "id", "TEXT");
        $list->addSearch($this->_('Заголовок'), 'title', 'TEXT');
        $list->addSearch($this->_('Дата'), 'date', 'DATE');

        $list->paintCondition = "'TCOL_04' == 'N'";
        $list->paintColor = "fafafa";

        $list->addURL = $this->app . "&edit=0";
        $list->editURL = $this->app . "&edit=TCOL_00";
        $list->deleteKey = "core_news.id";

        $list->showTable();
    }

    /**
     * Метод редактирования новости
     */
    public function action_edit_news() {
        $edit = new \editTable('core_news');
        $id = (int)$_GET['edit'];
        $edit->SQL="
                    SELECT id,
                    title,
                    content,
                    date
                    FROM core_news AS news
                    WHERE id = $id
        ";
        $edit->addControl($this->translate->tr("Заголовок:"), "TEXT", "maxlength=\"255\" size=\"60\"", "", "", true);
        //TODO: заменить TEXT на FCK_BASIC - сейчас приводит к бесконечной загрузке >_<
        $edit->addControl($this->translate->tr("Содержание:"), "TEXT", "cols=\"57\" rows=\"10\"", "", "", true);
        $edit->addControl($this->translate->tr("Дата и время публикации:"), "DATETIME", "maxlength=\"3\" size=\"2\"", "", "", true);
        $edit->addButton($this->translate->tr("Вернуться к списку новостей"), "load('$this->app')");

        // Не рабочий код сохранения
        $edit->save("xajax_SaveEvents(xajax.getFormValues(this.id))");
        $edit->showTable();
    }

    /**
     * Метод-индекс. Вызывается первым. Используется для разделения функций на страницы
     */
    public function action_index() {
        if (isset($_GET['edit']) && $_GET['edit'] != '') {
            $this->action_edit_news();
        } else {
            $this->action_show_news();
        }
    }
}