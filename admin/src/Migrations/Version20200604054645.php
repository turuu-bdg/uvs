<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200604054645 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE groupheader DROP FOREIGN KEY groupheader_ibfk_1');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE groupheader');
        $this->addSql('DROP TABLE maingroup');
        $this->addSql('DROP TABLE user_roll');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(180) NOT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON user (username)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE content (id INT AUTO_INCREMENT NOT NULL, content TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, date DATE NOT NULL, group_id INT NOT NULL, who INT NOT NULL, createdate DATETIME NOT NULL, abbrev VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, status INT NOT NULL, INDEX group_id (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE groupheader (id INT AUTO_INCREMENT NOT NULL, maingroup_id INT NOT NULL, name TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, abbrev TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX maingroup_id (maingroup_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE maingroup (id INT AUTO_INCREMENT NOT NULL, name TEXT CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, abbrev VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_roll (id INT AUTO_INCREMENT NOT NULL, roll VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE groupheader ADD CONSTRAINT groupheader_ibfk_1 FOREIGN KEY (maingroup_id) REFERENCES maingroup (id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649F85E0677 ON user');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, CHANGE roles roles INT NOT NULL');
    }
}
