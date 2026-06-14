import { MigrationInterface, QueryRunner } from "typeorm";

export class Migration1781477436925 implements MigrationInterface {
    name = 'Migration1781477436925'

    public async up(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.query(`ALTER TABLE \`vagas\` DROP FOREIGN KEY \`vagas_ibfk_1\``);
        await queryRunner.query(`ALTER TABLE \`candidaturas\` DROP FOREIGN KEY \`candidaturas_ibfk_1\``);
        await queryRunner.query(`ALTER TABLE \`candidaturas\` DROP FOREIGN KEY \`candidaturas_ibfk_2\``);
        await queryRunner.query(`DROP INDEX \`cnpj\` ON \`empresas\``);
        await queryRunner.query(`DROP INDEX \`email\` ON \`empresas\``);
        await queryRunner.query(`DROP INDEX \`empresa_id\` ON \`vagas\``);
        await queryRunner.query(`DROP INDEX \`uk_aluno_vaga\` ON \`candidaturas\``);
        await queryRunner.query(`DROP INDEX \`vaga_id\` ON \`candidaturas\``);
        await queryRunner.query(`DROP INDEX \`ra\` ON \`alunos\``);
        await queryRunner.query(`DROP INDEX \`email\` ON \`alunos\``);
        await queryRunner.query(`ALTER TABLE \`empresas\` ADD UNIQUE INDEX \`IDX_f5ed71aeb4ef47f95df5f8830b\` (\`cnpj\`)`);
        await queryRunner.query(`ALTER TABLE \`empresas\` ADD UNIQUE INDEX \`IDX_fe5e0374ec6d7d7dfbe0444690\` (\`email\`)`);
        await queryRunner.query(`ALTER TABLE \`empresas\` CHANGE \`telefone\` \`telefone\` varchar(20) NULL`);
        await queryRunner.query(`ALTER TABLE \`empresas\` CHANGE \`endereco\` \`endereco\` varchar(200) NULL`);
        await queryRunner.query(`ALTER TABLE \`empresas\` CHANGE \`area_atuacao\` \`area_atuacao\` varchar(100) NULL`);
        await queryRunner.query(`ALTER TABLE \`vagas\` CHANGE \`area\` \`area\` varchar(100) NULL`);
        await queryRunner.query(`ALTER TABLE \`vagas\` CHANGE \`requisitos\` \`requisitos\` varchar(150) NULL`);
        await queryRunner.query(`ALTER TABLE \`vagas\` CHANGE \`bolsa\` \`bolsa\` decimal(8,2) NULL`);
        await queryRunner.query(`ALTER TABLE \`vagas\` CHANGE \`local\` \`local\` varchar(150) NULL`);
        await queryRunner.query(`ALTER TABLE \`vagas\` CHANGE \`carga_horaria\` \`carga_horaria\` int NULL`);
        await queryRunner.query(`ALTER TABLE \`vagas\` CHANGE \`data_encerramento\` \`data_encerramento\` date NULL`);
        await queryRunner.query(`ALTER TABLE \`candidaturas\` CHANGE \`carta_apresentacao\` \`carta_apresentacao\` text NULL`);
        await queryRunner.query(`ALTER TABLE \`candidaturas\` CHANGE \`observacao\` \`observacao\` text NULL`);
        await queryRunner.query(`ALTER TABLE \`alunos\` ADD UNIQUE INDEX \`IDX_10966272854c55f95c9f941828\` (\`ra\`)`);
        await queryRunner.query(`ALTER TABLE \`alunos\` ADD UNIQUE INDEX \`IDX_1f9a8f3f4e5a314a2d7f828a60\` (\`email\`)`);
        await queryRunner.query(`ALTER TABLE \`alunos\` CHANGE \`telefone\` \`telefone\` varchar(20) NULL`);
        await queryRunner.query(`ALTER TABLE \`alunos\` CHANGE \`curso\` \`curso\` varchar(150) NULL`);
        await queryRunner.query(`ALTER TABLE \`alunos\` CHANGE \`periodo\` \`periodo\` int NULL`);
        await queryRunner.query(`ALTER TABLE \`vagas\` ADD CONSTRAINT \`FK_d8815ee22200784e3ae124da143\` FOREIGN KEY (\`empresa_id\`) REFERENCES \`empresas\`(\`id\`) ON DELETE NO ACTION ON UPDATE NO ACTION`);
        await queryRunner.query(`ALTER TABLE \`candidaturas\` ADD CONSTRAINT \`FK_25495b23c498b7fada81b549f6d\` FOREIGN KEY (\`aluno_id\`) REFERENCES \`alunos\`(\`id\`) ON DELETE NO ACTION ON UPDATE NO ACTION`);
        await queryRunner.query(`ALTER TABLE \`candidaturas\` ADD CONSTRAINT \`FK_97e1cfa7a2c7c81a1e4c7d1676c\` FOREIGN KEY (\`vaga_id\`) REFERENCES \`vagas\`(\`id\`) ON DELETE NO ACTION ON UPDATE NO ACTION`);
    }

    public async down(queryRunner: QueryRunner): Promise<void> {
        await queryRunner.query(`ALTER TABLE \`candidaturas\` DROP FOREIGN KEY \`FK_97e1cfa7a2c7c81a1e4c7d1676c\``);
        await queryRunner.query(`ALTER TABLE \`candidaturas\` DROP FOREIGN KEY \`FK_25495b23c498b7fada81b549f6d\``);
        await queryRunner.query(`ALTER TABLE \`vagas\` DROP FOREIGN KEY \`FK_d8815ee22200784e3ae124da143\``);
        await queryRunner.query(`ALTER TABLE \`alunos\` CHANGE \`periodo\` \`periodo\` int NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`alunos\` CHANGE \`curso\` \`curso\` varchar(150) NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`alunos\` CHANGE \`telefone\` \`telefone\` varchar(20) NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`alunos\` DROP INDEX \`IDX_1f9a8f3f4e5a314a2d7f828a60\``);
        await queryRunner.query(`ALTER TABLE \`alunos\` DROP INDEX \`IDX_10966272854c55f95c9f941828\``);
        await queryRunner.query(`ALTER TABLE \`candidaturas\` CHANGE \`observacao\` \`observacao\` text NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`candidaturas\` CHANGE \`carta_apresentacao\` \`carta_apresentacao\` text NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`vagas\` CHANGE \`data_encerramento\` \`data_encerramento\` date NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`vagas\` CHANGE \`carga_horaria\` \`carga_horaria\` int NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`vagas\` CHANGE \`local\` \`local\` varchar(150) NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`vagas\` CHANGE \`bolsa\` \`bolsa\` decimal(8,2) NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`vagas\` CHANGE \`requisitos\` \`requisitos\` varchar(150) NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`vagas\` CHANGE \`area\` \`area\` varchar(100) NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`empresas\` CHANGE \`area_atuacao\` \`area_atuacao\` varchar(100) NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`empresas\` CHANGE \`endereco\` \`endereco\` varchar(200) NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`empresas\` CHANGE \`telefone\` \`telefone\` varchar(20) NULL DEFAULT 'NULL'`);
        await queryRunner.query(`ALTER TABLE \`empresas\` DROP INDEX \`IDX_fe5e0374ec6d7d7dfbe0444690\``);
        await queryRunner.query(`ALTER TABLE \`empresas\` DROP INDEX \`IDX_f5ed71aeb4ef47f95df5f8830b\``);
        await queryRunner.query(`CREATE UNIQUE INDEX \`email\` ON \`alunos\` (\`email\`)`);
        await queryRunner.query(`CREATE UNIQUE INDEX \`ra\` ON \`alunos\` (\`ra\`)`);
        await queryRunner.query(`CREATE INDEX \`vaga_id\` ON \`candidaturas\` (\`vaga_id\`)`);
        await queryRunner.query(`CREATE UNIQUE INDEX \`uk_aluno_vaga\` ON \`candidaturas\` (\`aluno_id\`, \`vaga_id\`)`);
        await queryRunner.query(`CREATE INDEX \`empresa_id\` ON \`vagas\` (\`empresa_id\`)`);
        await queryRunner.query(`CREATE UNIQUE INDEX \`email\` ON \`empresas\` (\`email\`)`);
        await queryRunner.query(`CREATE UNIQUE INDEX \`cnpj\` ON \`empresas\` (\`cnpj\`)`);
        await queryRunner.query(`ALTER TABLE \`candidaturas\` ADD CONSTRAINT \`candidaturas_ibfk_2\` FOREIGN KEY (\`vaga_id\`) REFERENCES \`vagas\`(\`id\`) ON DELETE CASCADE ON UPDATE RESTRICT`);
        await queryRunner.query(`ALTER TABLE \`candidaturas\` ADD CONSTRAINT \`candidaturas_ibfk_1\` FOREIGN KEY (\`aluno_id\`) REFERENCES \`alunos\`(\`id\`) ON DELETE CASCADE ON UPDATE RESTRICT`);
        await queryRunner.query(`ALTER TABLE \`vagas\` ADD CONSTRAINT \`vagas_ibfk_1\` FOREIGN KEY (\`empresa_id\`) REFERENCES \`empresas\`(\`id\`) ON DELETE CASCADE ON UPDATE RESTRICT`);
    }

}
