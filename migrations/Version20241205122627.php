<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205122627 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE approved (id INT AUTO_INCREMENT NOT NULL, request_id INT NOT NULL, team_leader_id INT NOT NULL, project_manager_id INT NOT NULL, date_approved_tl DATETIME NOT NULL, date_approved_pm DATETIME NOT NULL, status_team_leader VARCHAR(50) NOT NULL, status_project_manager VARCHAR(50) NOT NULL, INDEX IDX_7C57D81D427EB8A5 (request_id), INDEX IDX_7C57D81DC4105033 (team_leader_id), INDEX IDX_7C57D81D60984F51 (project_manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employee (id INT AUTO_INCREMENT NOT NULL, role_id INT DEFAULT NULL, team_id INT DEFAULT NULL, job_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birth_date DATETIME NOT NULL, vacation_days INT NOT NULL, email VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_5D9F75A1E7927C74 (email), UNIQUE INDEX UNIQ_5D9F75A1F85E0677 (username), INDEX IDX_5D9F75A1D60322AC (role_id), INDEX IDX_5D9F75A1296CD8AE (team_id), INDEX IDX_5D9F75A1BE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE request (id INT AUTO_INCREMENT NOT NULL, employee_id INT NOT NULL, number_of_days INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, status VARCHAR(50) NOT NULL, comment VARCHAR(200) NOT NULL, created_date DATETIME NOT NULL, INDEX IDX_3B978F9F8C03F15C (employee_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE approved ADD CONSTRAINT FK_7C57D81D427EB8A5 FOREIGN KEY (request_id) REFERENCES request (id)');
        $this->addSql('ALTER TABLE approved ADD CONSTRAINT FK_7C57D81DC4105033 FOREIGN KEY (team_leader_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE approved ADD CONSTRAINT FK_7C57D81D60984F51 FOREIGN KEY (project_manager_id) REFERENCES employee (id)');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1296CD8AE FOREIGN KEY (team_id) REFERENCES team (id)');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE request ADD CONSTRAINT FK_3B978F9F8C03F15C FOREIGN KEY (employee_id) REFERENCES employee (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE approved DROP FOREIGN KEY FK_7C57D81D427EB8A5');
        $this->addSql('ALTER TABLE approved DROP FOREIGN KEY FK_7C57D81DC4105033');
        $this->addSql('ALTER TABLE approved DROP FOREIGN KEY FK_7C57D81D60984F51');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1D60322AC');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1296CD8AE');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1BE04EA9');
        $this->addSql('ALTER TABLE request DROP FOREIGN KEY FK_3B978F9F8C03F15C');
        $this->addSql('DROP TABLE approved');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE request');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
