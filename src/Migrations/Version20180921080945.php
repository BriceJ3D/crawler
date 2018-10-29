<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180921080945 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE domain (id INT AUTO_INCREMENT NOT NULL, domain_name VARCHAR(255) NOT NULL, dispo VARCHAR(255) DEFAULT NULL, expiration_date DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE research (id INT AUTO_INCREMENT NOT NULL, search_date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE research_tag (research_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_B69F48347909E1ED (research_id), INDEX IDX_B69F4834BAD26311 (tag_id), PRIMARY KEY(research_id, tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE url (id INT AUTO_INCREMENT NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE research_tag ADD CONSTRAINT FK_B69F48347909E1ED FOREIGN KEY (research_id) REFERENCES research (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE research_tag ADD CONSTRAINT FK_B69F4834BAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE research_tag DROP FOREIGN KEY FK_B69F48347909E1ED');
        $this->addSql('ALTER TABLE research_tag DROP FOREIGN KEY FK_B69F4834BAD26311');
        $this->addSql('DROP TABLE domain');
        $this->addSql('DROP TABLE research');
        $this->addSql('DROP TABLE research_tag');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE url');
    }
}
