<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200427091337 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE users CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE commande ADD commentaire LONGTEXT DEFAULT NULL, CHANGE jour_distrib_id jour_distrib_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE jour_distrib CHANGE poid_restant poid_restant DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_commande CHANGE commande_id commande_id INT DEFAULT NULL, CHANGE pain_id pain_id INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE commande DROP commentaire, CHANGE jour_distrib_id jour_distrib_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE jour_distrib CHANGE poid_restant poid_restant DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE ligne_commande CHANGE commande_id commande_id INT DEFAULT NULL, CHANGE pain_id pain_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`');
    }
}
