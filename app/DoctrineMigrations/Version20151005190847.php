<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20151005190847 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('INSERT INTO `settings` (`_key`, `value`, `type`) VALUES ("offline_print_text", "<ul><li>Для оплаты обучения в автошколе Вам достаточно скачать и распечатать эту квитанцию и предоставить ее в офисе автошколы.</li><li>Оплата принимается наличными только за полный курс обучения, касса находится в центральном офисе автошколы, который находится по адресу: м. \"Марксистская\" или \"Таганская\", Товарищеский пер, д.3.</li><li>Офис работает в будни с 10:00 до 21:00, в субботу - с 10:00 до 17:00.</li><li>При записи в автошколу потребуется Ваш паспорт. Возьмите его с собой!</li><li>Отдельно оплачиваются: учебные материалы (2050 рублей) и медицинская справка (1200 рублей).</li><li>После оплаты Вы сможете обучаться как очно (в выбранном филиале), так и дистанционно (на сайте).</li></ul>", "string");');
    }

    public function down(Schema $schema)
    {
        $this->addSql('DELETE FROM `settings` WHERE `_key`="offline_print_text";');
    }
}
