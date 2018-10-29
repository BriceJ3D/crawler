<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180926085931 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE url_research (url_id INT NOT NULL, research_id INT NOT NULL, INDEX IDX_358B3E4B81CFDAE7 (url_id), INDEX IDX_358B3E4B7909E1ED (research_id), PRIMARY KEY(url_id, research_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE url_research ADD CONSTRAINT FK_358B3E4B81CFDAE7 FOREIGN KEY (url_id) REFERENCES url (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE url_research ADD CONSTRAINT FK_358B3E4B7909E1ED FOREIGN KEY (research_id) REFERENCES research (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE url_research');
    }
}
