<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521082511 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rating_history ADD rating LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', DROP rating_up, DROP rating_down');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rating_history ADD rating_down LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', CHANGE rating rating_up LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\'');
    }
}
