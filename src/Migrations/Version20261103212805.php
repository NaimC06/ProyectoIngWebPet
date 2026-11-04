<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261103212805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE perro (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nombre VARCHAR(80) NOT NULL, raza VARCHAR(80) NOT NULL, edad INTEGER NOT NULL, tamano VARCHAR(20) NOT NULL, propietario_id INTEGER NOT NULL, CONSTRAINT FK_A479CB6053C8D32C FOREIGN KEY (propietario_id) REFERENCES usuario (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A479CB6053C8D32C ON perro (propietario_id)');
        $this->addSql('ALTER TABLE usuario ADD COLUMN telefono VARCHAR(15) DEFAULT NULL');
        $this->addSql('ALTER TABLE usuario ADD COLUMN zona_trabajo VARCHAR(80) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE perro');
        $this->addSql('CREATE TEMPORARY TABLE __temp__usuario AS SELECT id, nombre, apellido, email, roles, password, cedula, activo FROM usuario');
        $this->addSql('DROP TABLE usuario');
        $this->addSql('CREATE TABLE usuario (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nombre VARCHAR(100) NOT NULL, apellido VARCHAR(100) NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, cedula VARCHAR(10) DEFAULT NULL, activo BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO usuario (id, nombre, apellido, email, roles, password, cedula, activo) SELECT id, nombre, apellido, email, roles, password, cedula, activo FROM __temp__usuario');
        $this->addSql('DROP TABLE __temp__usuario');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2265B05DE7927C74 ON usuario (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2265B05D7BF39BE0 ON usuario (cedula)');
    }
}
