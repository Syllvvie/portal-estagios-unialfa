<?php

class Painel
{
    private string $apiUrl = "http://localhost:3000";

    // ─── Método base ──────────────────────────────────────────────────────────

    public function requisitar(string $metodo, string $endpoint, array $dados = [], string $token = ''): array
    {
        $url     = $this->apiUrl . $endpoint;
        $headers = ['Content-Type: application/json'];

        if ($token !== '') {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($metodo));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if (!empty($dados) && in_array(strtoupper($metodo), ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dados));
        }

        $resposta  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $erroCurl  = curl_error($ch);
        curl_close($ch);

        if ($erroCurl) {
            return ['_erro' => 'Erro de conexao com a API: ' . $erroCurl, '_httpCode' => 0];
        }

        $json = json_decode($resposta, true) ?? [];
        $json['_httpCode'] = $httpCode;
        return $json;
    }

    // ─── Session / Login ──────────────────────────────────────────────────────

    /**
     * Login do aluno: POST /session/aluno  { ra, senha }
     * Retorna: { token, aluno: { id, ra, nome, email, ... } }
     */
    public function loginAluno(string $ra, string $senha): array
    {
        return $this->requisitar('POST', '/session/aluno', [
            'ra'    => $ra,
            'senha' => $senha,
        ]);
    }

    /**
     * Login da empresa: POST /session/empresa  { email, senha }
     * Retorna: { token, empresa: { id, nome, email, status, ... } }
     */
    public function loginEmpresa(string $email, string $senha): array
    {
        return $this->requisitar('POST', '/session/empresa', [
            'email' => $email,
            'senha' => $senha,
        ]);
    }

    // ─── Alunos ───────────────────────────────────────────────────────────────

    /** GET /alunos  (público) → { alunos: [...] } */
    public function listarAlunos(): array
    {
        return $this->requisitar('GET', '/alunos');
    }

    /** GET /alunos/perfil  (token aluno) → { aluno: {...} } */
    public function perfilAluno(string $token): array
    {
        return $this->requisitar('GET', '/alunos/perfil', [], $token);
    }

    /** POST /alunos  (público) → cadastro */
    public function cadastrarAluno(array $dados): array
    {
        return $this->requisitar('POST', '/alunos', $dados);
    }

    /**
     * PUT /alunos/trocar-senha  (token aluno)
     * Body: { senha_atual, nova_senha }
     */
    public function trocarSenha(string $novaSenha, string $token): array
    {
        return $this->requisitar('PUT', '/alunos/trocar-senha', [
            'nova_senha' => $novaSenha,
        ], $token);
    }

    // ─── Empresas ─────────────────────────────────────────────────────────────

    /** POST /empresas  (público) → cadastro */
    public function cadastrarEmpresa(array $dados): array
    {
        return $this->requisitar('POST', '/empresas', $dados);
    }

    /** GET /empresas  (público) → { empresas: [...] } */
    public function listarEmpresas(): array
    {
        return $this->requisitar('GET', '/empresas');
    }

    /** GET /empresas/perfil  (token empresa) → { empresa: {...} } */
    public function perfilEmpresa(string $token): array
    {
        return $this->requisitar('GET', '/empresas/perfil', [], $token);
    }

    /** PUT /empresas/:id  (token empresa) → atualizar dados */
    public function atualizarEmpresa(int $id, array $dados, string $token): array
    {
        return $this->requisitar('PUT', '/empresas/' . $id, $dados, $token);
    }

    // ─── Vagas ────────────────────────────────────────────────────────────────

    /** GET /vagas?ativas=true  (público) → { vagas: [...] } */
    public function listarVagas(bool $apenasAtivas = true): array
    {
        $q = $apenasAtivas ? '?ativas=true' : '';
        return $this->requisitar('GET', '/vagas' . $q);
    }

    /** GET /vagas/minhas  (token empresa) → { vagas: [...] } */
    public function minhasVagas(string $token): array
    {
        return $this->requisitar('GET', '/vagas/minhas', [], $token);
    }

    /** GET /vagas/:id  (público) → { vaga: {...} } */
    public function buscarVaga(int $id): array
    {
        return $this->requisitar('GET', '/vagas/' . $id);
    }

    /** POST /vagas  (token empresa) → { vaga: {...} } */
    public function criarVaga(array $dados, string $token): array
    {
        return $this->requisitar('POST', '/vagas', $dados, $token);
    }

    /** PUT /vagas/:id  (token empresa) */
    public function atualizarVaga(int $id, array $dados, string $token): array
    {
        return $this->requisitar('PUT', '/vagas/' . $id, $dados, $token);
    }

    /** DELETE /vagas/:id  (token empresa) */
    public function removerVaga(int $id, string $token): array
    {
        return $this->requisitar('DELETE', '/vagas/' . $id, [], $token);
    }

    // ─── Candidaturas ─────────────────────────────────────────────────────────

    /** GET /candidaturas/minhas  (token aluno) → { candidaturas: [...] } */
    public function minhasCandidaturas(string $token): array
    {
        return $this->requisitar('GET', '/candidaturas/minhas', [], $token);
    }

    /** GET /candidaturas/vaga/:vaga_id  (token empresa) → { candidaturas: [...] } */
    public function candidatosDaVaga(int $vaga_id, string $token): array
    {
        return $this->requisitar('GET', '/candidaturas/vaga/' . $vaga_id, [], $token);
    }

    /**
     * POST /candidaturas  (token aluno)
     * Body: { vaga_id: int, carta_apresentacao?: string }
     */
    public function candidatar(int $vaga_id, string $token, string $carta = ''): array
    {
        $dados = ['vaga_id' => $vaga_id];
        if ($carta !== '') $dados['carta_apresentacao'] = $carta;
        return $this->requisitar('POST', '/candidaturas', $dados, $token);
    }

    /**
     * PATCH /candidaturas/:id/status  (token empresa)
     * Body: { status: string, observacao?: string }
     */
    public function atualizarStatusCandidatura(int $id, string $status, string $token, string $obs = ''): array
    {
        $dados = ['status' => $status];
        if ($obs !== '') $dados['observacao'] = $obs;
        return $this->requisitar('PATCH', '/candidaturas/' . $id . '/status', $dados, $token);
    }

    /** PATCH /candidaturas/:id/cancelar  (token aluno) */
    public function cancelarCandidatura(int $id, string $token): array
    {
        return $this->requisitar('PATCH', '/candidaturas/' . $id . '/cancelar', [], $token);
    }


    // ─── Notificações ─────────────────────────────────────────────────────────

    /** GET /notificacoes  (token aluno ou empresa) → { notificacoes: [...] } */
    public function minhasNotificacoes(string $token): array
    {
        return $this->requisitar('GET', '/notificacoes', [], $token);
    }

    /** PATCH /notificacoes/:id/lida  (token) */
    public function marcarNotificacaoLida(int $id, string $token): array
    {
        return $this->requisitar('PATCH', '/notificacoes/' . $id . '/lida', [], $token);
    }
}
