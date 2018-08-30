<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180321163235 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE scanned_qrcode ADD event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE scanned_qrcode ADD CONSTRAINT FK_80281E871F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('CREATE INDEX IDX_80281E871F7E88B ON scanned_qrcode (event_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE scanned_qrcode DROP FOREIGN KEY FK_80281E871F7E88B');
        $this->addSql('DROP INDEX IDX_80281E871F7E88B ON scanned_qrcode');
        $this->addSql('ALTER TABLE scanned_qrcode DROP event_id');
    }
}
