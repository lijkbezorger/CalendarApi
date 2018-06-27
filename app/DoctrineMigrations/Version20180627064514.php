<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180627064514 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE event (id SMALLINT UNSIGNED AUTO_INCREMENT NOT NULL, user_id SMALLINT UNSIGNED DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, start_date_time DATETIME NOT NULL, end_date_time DATETIME NOT NULL, discriminator VARCHAR(255) NOT NULL, INDEX IDX_3BAE0AA7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE appointment_user (appointment_id SMALLINT UNSIGNED NOT NULL, user_id SMALLINT UNSIGNED NOT NULL, INDEX IDX_9E501E88E5B533F9 (appointment_id), INDEX IDX_9E501E88A76ED395 (user_id), PRIMARY KEY(appointment_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE appointment_user ADD CONSTRAINT FK_9E501E88E5B533F9 FOREIGN KEY (appointment_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment_user ADD CONSTRAINT FK_9E501E88A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE appointment_user DROP FOREIGN KEY FK_9E501E88E5B533F9');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE appointment_user');
    }
}
