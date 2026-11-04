<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261104034108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paseo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fecha_hora DATETIME NOT NULL, precio NUMERIC(10, 2) DEFAULT NULL, estado VARCHAR(20) NOT NULL, observaciones CLOB DEFAULT NULL, creado_en DATETIME NOT NULL, cliente_id INTEGER NOT NULL, perro_id INTEGER NOT NULL, paseador_id INTEGER DEFAULT NULL, CONSTRAINT FK_2F5AB296DE734E51 FOREIGN KEY (cliente_id) REFERENCES usuario (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2F5AB2962F41C9A9 FOREIGN KEY (perro_id) REFERENCES perro (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2F5AB296755432A FOREIGN KEY (paseador_id) REFERENCES usuario (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_2F5AB296DE734E51 ON paseo (cliente_id)');
        $this->addSql('CREATE INDEX IDX_2F5AB2962F41C9A9 ON paseo (perro_id)');
        $this->addSql('CREATE INDEX IDX_2F5AB296755432A ON paseo (paseador_id)');
        $this->addSql('CREATE TABLE "plan" (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nombre VARCHAR(80) NOT NULL, precio NUMERIC(10, 2) NOT NULL, cantidad_paseos INTEGER NOT NULL, duracion_dias INTEGER NOT NULL, estado VARCHAR(20) NOT NULL, creado_en DATETIME NOT NULL)');
        $this->addSql('CREATE TABLE suscripcion (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fecha_inicio DATE NOT NULL, fecha_fin DATE NOT NULL, paseos_incluidos INTEGER NOT NULL, paseos_utilizados INTEGER NOT NULL, estado VARCHAR(20) NOT NULL, creado_en DATETIME NOT NULL, usuario_id INTEGER NOT NULL, plan_id INTEGER NOT NULL, CONSTRAINT FK_497FA0DB38439E FOREIGN KEY (usuario_id) REFERENCES usuario (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_497FA0E899029B FOREIGN KEY (plan_id) REFERENCES "plan" (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_497FA0DB38439E ON suscripcion (usuario_id)');
        $this->addSql('CREATE INDEX IDX_497FA0E899029B ON suscripcion (plan_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE paseo');
        $this->addSql('DROP TABLE "plan"');
        $this->addSql('DROP TABLE suscripcion');
    }
}
