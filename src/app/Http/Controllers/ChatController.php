<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use App\Models\Contact;
use App\Models\Message;
use Exception;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    
    /**
     * Página principal do chat
     */
    public function index(Request $request)
    {
        try {
            if ($request->modalChannel) {
                return $this->modalContacts($request);
            }

            return Inertia::render('Index', $this->getIndexData($request));
        } catch (Exception $e) {
            Log::error("Erro ao listar mensagens", [
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return back()->withErrors([
                'error' => 'Não foi possível listar as mensagens!'
            ]);
        }
    }

    /**
     * Marca mensagem como lida
     */
    public function readMessage(Request $request)
    {
        try {
            $request->validate(['contact_id' => 'required']);
            Message::markAsRead($request->contact_id);

            return redirect()->back()->with(['success' => true]);
        } catch(Exception $e) {
            Log::error("Erro ao ler mensagem", ['exception' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Não foi possível ler a mensagem.']);
        }
    }
    /**
     * Envia mensagem
     */
    public function sendMessage(Request $request)
    {
        try {
            $request->validate([
                'contact_id' => 'required',
                'message' => 'required'
            ]);

            Message::send($request->contact_id, $request->message);

            return redirect()->back()->with([
                'success' => true,
                'contact_id' => $request->contact_id,
            ]);
        } catch(Exception $e) {
            Log::error("Erro ao enviar mensagem", ['exception' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Não foi possível enviar a mensagem.']);
        }
    }

    /**
     * Busca contatos para o modal
     */
    public function modalContacts(Request $request)
    {
        try {
            if (!$request->has('modalChannel') || empty($request->modalChannel)) {
                throw new Exception("o canal é obrigatório");
            }

            $modalChannel = $request->query('modalChannel');

            $contactsQuery = Contact::with('channel');

            $channelId = Channel::where('name', $modalChannel)->value('id');
            if ($channelId) {
                $contactsQuery->where('channel_id', $channelId);
            }

            $modalContacts = $contactsQuery->get();

            $pageData = $this->getIndexData($request);

            $pageData['modalContacts'] = $modalContacts;

            return Inertia::render('Index', $pageData);

        } catch (Exception $e) {
            Log::error("Erro ao buscar contatos do modal", [
                'exception' => $e->getMessage(),
                'request' => $request->all()
            ]);
            return back()->withErrors([
                'error' => 'Não foi possível carregar os contatos do modal.'
            ]);
        }
    }

    /**
     * Obtém dados para a página principal
     */
    private function getIndexData(Request $request)
    {
        $channels = Channel::pluck('name')->toArray();
        $contacts = Contact::forChannel($request->query('channel', 'all'));
        $messages = $request->contact_id ? Message::forContact($request->contact_id) : [];

        return compact('channels', 'contacts', 'messages');
    }
}
