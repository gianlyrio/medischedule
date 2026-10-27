<div align="center">

# 🩺 **M E D I S C H E D U L E**
> **Plataforma Académica de Gestão e Agendamento Clínico**

[🛡️ Arquitetura Segura](#) • [⚡ Laravel 13](#) • [🧪 100% Testado](#)

</div>

---

Relatório de Desenvolvimento e Arquitetura do Sistema

O **MediSchedule** foi desenvolvido como uma solução robusta em **Laravel 13** para gerenciar o fluxo completo de agendamentos médicos, unindo padrões acadêmicos de engenharia de software, segurança de dados e validações estritas de regras de negócio.

### 1. Arquitetura e Tecnologias
* **Framework**: Laravel 13 (PHP 8.5) adotando o padrão MVC (Model-View-Controller) combinado com Camada de Serviço (`Service Layer`).
* **Persistência de Dados**: SQLite, utilizando migrações relacionais estritas com restrições de integridade (`foreign keys`, restrições `NOT NULL` em campos críticos como `cpf` e `crm`).
* **Interface (UI/UX)**: Bootstrap 5 totalmente responsivo, contemplando painéis segregados para Administradores, Médicos e Pacientes, complementados por modais dinâmicos.

### 2. Regras de Negócio e Lógica Implementada (`AppointmentService`)
A lógica central do sistema foi isolada em uma camada de serviço dedicada (`AppointmentService`), garantindo alta coesão e testabilidade:
* **Prevenção de Agendamentos Retroativos**: Validação temporal rigorosa que rejeita qualquer tentativa de agendamento para datas ou horários anteriores ao momento atual.
* **Gestão de Disponibilidade e Conflitos**: Bloqueio de sobreposição de horários para o mesmo profissional, respeitando os turnos e a duração dos blocos de atendimento (`slot_duration`).
* **Política de Cancelamento (24h)**: Regra de negócio crítica que impede o cancelamento de consultas caso o intervalo entre a solicitação e o horário agendado seja inferior a 24 horas.
* **Controle de Acesso Baseado em Papéis (RBAC)**: Proteção de rotas sensíveis por meio de middlewares personalizados (`auth` e verificação de `role`), garantindo que pacientes acedam apenas aos seus próprios agendamentos e médicos/admins gerenciem o sistema adequadamente.

### 3. Testes Automatizados e Qualidade de Software
Para atender aos critérios de validação acadêmica exigidos, foi construída uma suíte de testes de funcionalidade (`Feature Tests`) utilizando o **PHPUnit** e a trait `RefreshDatabase` (com SQLite em memória):
* **Validação de Agendamento Retroativo**: Assegura que o sistema lança a exceção correta ao tentar marcar consultas no passado.
* **Validação da Regra de Cancelamento**: Verifica se a restrição de antecedência mínima de 24 horas bloqueia cancelamentos prematuros.
* **Cobertura Verde (100% Passed)**: Todos os testes de regras de negócio e integridade estrutural executam e passam com sucesso através do comando nativo do framework.

---

<div align="center">
  <p><strong>SOFTWARE MEDISCHEDULE DESENVOLVIDO POR GIAN LYRIO © 2026</strong></p>
</div>