<?php

declare(strict_types=1);

namespace Streamlabs\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190323201857 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Creates streamer\'s event log table';
    }

    public function up(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE streamers_event_log
            (
                id int(11) auto_increment,
                streamer_id int(11) not null,
                event_type varchar(100) not null,
                event_data mediumtext not null,
                created_at datetime not null,
                PRIMARY KEY (id),
                CONSTRAINT FK_streamer_id FOREIGN KEY (streamer_id)
                REFERENCES user_to_streamer(streamer_id)
            ); ENGINE=InnoDB DEFAULT CHARSET=latin1;'
        );

    }

    public function down(Schema $schema) : void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE IF EXISTS streamers_event_log;');

    }
}
