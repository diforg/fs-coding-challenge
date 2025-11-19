<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contact;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use Exception;

class GenerateMessagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera uma nova mensagem através de um novo contato com delay de 1 a 3 segundos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('🎯 Iniciando geração de mensagem...');

            // Gera um delay aleatório entre 1 e 3 segundos
            $delay = rand(1, 3);
            $this->info("⏳ Aguardando {$delay} segundo(s)...");
            
            // Barra de progresso para o delay
            $this->output->progressStart($delay);
            for ($i = 0; $i < $delay; $i++) {
                sleep(1);
                $this->output->progressAdvance();
            }
            $this->output->progressFinish();

            // Usa uma transação para garantir a consistência dos dados
            DB::transaction(function () {
                $this->info('📝 Criando novo contato...');
                
                // Cria um novo contato usando a factory
                $contact = Contact::factory()->create();
                $this->info("✅ Contato criado - ID: {$contact->id}, Nome: {$contact->name}");

                $this->info('💬 Criando mensagem associada...');
                
                // Cria uma mensagem associada ao contato
                $message = Message::factory()->create([
                    'contact_id' => $contact->id,
                ]);
                
                $this->info("✅ Mensagem criada - ID: {$message->id}");
                $this->info("📊 Resumo:");
                $this->line("   • Contato: {$contact->name} ({$contact->identifier})");
                $this->line("   • Mensagem: " . substr($message->message, 0, 50) . "...");
            });

            $this->info('🎉 Mensagem gerada com sucesso!');

        } catch (Exception $e) {
            $this->error('❌ Erro ao gerar mensagem: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}