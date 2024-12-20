<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241219220907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE approved CHANGE team_leader_id team_leader_id INT DEFAULT NULL, CHANGE project_manager_id project_manager_id INT DEFAULT NULL, CHANGE date_approved_tl date_approved_tl DATETIME DEFAULT NULL, CHANGE date_approved_pm date_approved_pm DATETIME DEFAULT NULL, CHANGE status_team_leader status_team_leader VARCHAR(50) DEFAULT NULL, CHANGE status_project_manager status_project_manager VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE approved CHANGE team_leader_id team_leader_id INT NOT NULL, CHANGE project_manager_id project_manager_id INT NOT NULL, CHANGE date_approved_tl date_approved_tl DATETIME NOT NULL, CHANGE date_approved_pm date_approved_pm DATETIME NOT NULL, CHANGE status_team_leader status_team_leader VARCHAR(50) NOT NULL, CHANGE status_project_manager status_project_manager VARCHAR(50) NOT NULL');
    }
}
