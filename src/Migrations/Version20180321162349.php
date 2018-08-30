<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180321162349 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE scanned_qrcode (id INT AUTO_INCREMENT NOT NULL, classic_user_id INT DEFAULT NULL, date_scan_qr_code DATETIME DEFAULT NULL, INDEX IDX_80281E83BD1290E (classic_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE scanned_qrcode ADD CONSTRAINT FK_80281E83BD1290E FOREIGN KEY (classic_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user DROP date_scan_qr_code');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE scanned_qrcode');
        $this->addSql('ALTER TABLE user ADD date_scan_qr_code DATETIME DEFAULT NULL');
    }
}
