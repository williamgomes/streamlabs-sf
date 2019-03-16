<?php

declare(strict_types=1);

namespace Streamlabs\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190313213728 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creating users table';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users
            (
                id int(11) auto_increment,
                twitch_id int(11) not null,
                twitch_login varchar(100) not null,
                twitch_display_name varchar(100) not null,
                twitch_broadcaster_type varchar(100) not null,
                last_login datetime not null,
                created_at timestamp not null,
                PRIMARY KEY (id)
            ); ENGINE=InnoDB DEFAULT CHARSET=latin1;'
        );

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE IF EXISTS users;');

    }
}
