<?php

require_once DOC_ROOT . "core2/inc/ajax.func.php";

class ModAjax extends ajaxFunc {

    public function __construct(xajaxResponse $res) {
        parent::__construct($res);
    }

    /**
     * Метод для обработки AJAX-запроса для сохранения новости
     * @param array $data - данные формы
     * @return xajaxResponse - ответ будет обработан js функцией ядра
     */
    public function axSaveEvents($data) {
        $fields = array(
            'title' => 'req',
            'content' => 'req',
            'date' => 'req',
        );
        if ($this->ajaxValidate($data, $fields)) {
            return $this->response;
        }

        $errors = array();
        try {
            // Код обработки данных формы и сохранения в базу данных
            $newsId = isset($data['params']['edit']) ? $data['params']['edit'] : 0;

            // Проверяем, является ли новость новой или уже существующей
            if ($newsId == 0) {
                $this->db->insert('core_news', $data['control']);
            } else {
                $this->db->update('core_news', $data['control'], ['id = ?' => $newsId]);
            }
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        //TODO: актуализировать AJAX-обработчик ошибок по примеру других модулей
        if (count($errors) == 0) {
            $this->done($data);
        } else {
            $msgerror = implode(", ", $errors);
            $this->error[] = $msgerror;
            $this->displayError($data);
        }
        return $this->response;
    }
}