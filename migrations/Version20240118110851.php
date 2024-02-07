<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240118110851 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `admin` (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', profile_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, userslug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_880E0D76E7927C74 (email), UNIQUE INDEX UNIQ_880E0D76CCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chapter (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', courses_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', instructor_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, chapterslug VARCHAR(255) NOT NULL, INDEX IDX_F981B52EF9295384 (courses_id), INDEX IDX_F981B52E8C4FC193 (instructor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, status INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', resolved_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_4C62E638E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE courses (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', instructor_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, duration INT NOT NULL, value NUMERIC(5, 2) DEFAULT NULL, last_accessed DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', courseslug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_A9A55A4C8C4FC193 (instructor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enrollment (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', student_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', courses_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', date_enrolled DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_completed DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DBDCD7E1CB944F1A (student_id), INDEX IDX_DBDCD7E1F9295384 (courses_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE instructor (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', profile_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, userslug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_31FC43DDE7927C74 (email), UNIQUE INDEX UNIQ_31FC43DDCCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', chapter_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', instructor_id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, status VARCHAR(255) DEFAULT NULL, duration TIME NOT NULL, INDEX IDX_F87474F3579F4768 (chapter_id), INDEX IDX_F87474F38C4FC193 (instructor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletter_sub (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profile (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', date_of_birth DATETIME DEFAULT NULL, country VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reviews (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', course_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', student_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, review VARCHAR(255) NOT NULL, reviewslug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6970EB0F591CC992 (course_id), INDEX IDX_6970EB0FCB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', profile_id CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) DEFAULT NULL, userslug VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_B723AF33E7927C74 (email), UNIQUE INDEX UNIQ_B723AF33CCFA12B8 (profile_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE testimonial (id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, testimonial VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_E6BDCDF7E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `admin` ADD CONSTRAINT FK_880E0D76CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52EF9295384 FOREIGN KEY (courses_id) REFERENCES courses (id)');
        $this->addSql('ALTER TABLE chapter ADD CONSTRAINT FK_F981B52E8C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id)');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4C8C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id)');
        $this->addSql('ALTER TABLE enrollment ADD CONSTRAINT FK_DBDCD7E1CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE enrollment ADD CONSTRAINT FK_DBDCD7E1F9295384 FOREIGN KEY (courses_id) REFERENCES courses (id)');
        $this->addSql('ALTER TABLE instructor ADD CONSTRAINT FK_31FC43DDCCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F3579F4768 FOREIGN KEY (chapter_id) REFERENCES chapter (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F38C4FC193 FOREIGN KEY (instructor_id) REFERENCES instructor (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F591CC992 FOREIGN KEY (course_id) REFERENCES courses (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FCB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` DROP FOREIGN KEY FK_880E0D76CCFA12B8');
        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52EF9295384');
        $this->addSql('ALTER TABLE chapter DROP FOREIGN KEY FK_F981B52E8C4FC193');
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4C8C4FC193');
        $this->addSql('ALTER TABLE enrollment DROP FOREIGN KEY FK_DBDCD7E1CB944F1A');
        $this->addSql('ALTER TABLE enrollment DROP FOREIGN KEY FK_DBDCD7E1F9295384');
        $this->addSql('ALTER TABLE instructor DROP FOREIGN KEY FK_31FC43DDCCFA12B8');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F3579F4768');
        $this->addSql('ALTER TABLE lesson DROP FOREIGN KEY FK_F87474F38C4FC193');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F591CC992');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0FCB944F1A');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33CCFA12B8');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE chapter');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE courses');
        $this->addSql('DROP TABLE enrollment');
        $this->addSql('DROP TABLE instructor');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE newsletter_sub');
        $this->addSql('DROP TABLE profile');
        $this->addSql('DROP TABLE reviews');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE testimonial');
    }
}
