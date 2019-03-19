<?php

declare(strict_types=1);

namespace Streamlabs\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190313230827 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create user_to_streamer table to save user\'s favorite streamer info';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user_to_streamer
            (
                id int(11) auto_increment,
                user_id int(11) not null,
                streamer_id int(11) not null,
                streamer_name varchar(100) not null,
                streamer_display_name varchar(100) not null,
                updated_at timestamp not null,
                created_at datetime not null,
                PRIMARY KEY (id),
                CONSTRAINT FK_user_id FOREIGN KEY (user_id)
                REFERENCES users(id)
            ); ENGINE=InnoDB DEFAULT CHARSET=latin1;'
        );

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE IF EXISTS user_to_streamer;');

    }
}
