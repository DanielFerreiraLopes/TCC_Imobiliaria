<?php

namespace App\Http\Controllers;

use App\Models\Imovel;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function loginView()
    {
        return view('log/login');
    }

    public function login(Request $request)
    {
        $email = $request->input('email');
        $senha = $request->input('senha');

        $usuario = Usuario::where('email', $email)->where('senha', $senha)->first();

        if ($usuario) {
            Session::put('info_usuario', $usuario);
            return redirect('/');
        } else {
            return redirect('/login')->with('mensagem_erro', "Usuario não encontrado");
        }
    }

    public function logout()
    {

        Session::flush();

        return redirect('/login');
    }


    public function cadastroView()
    {
        return view('log/cadastro');
    }



    public function cadastro(Request $request)
    {
        $nome = $request->input('nome');
        $telefone = $request->input('tele');
        $email = $request->input('email');
        $conemail = $request->input('confirmemail');
        $senha = $request->input('senha');

        if ($email != $conemail) {
            return redirect('/cadastro')->with('mensagem_erro', "A confirmação de email esta incorreta");
        }

        $confirma1 = Usuario::where('email', $email)->first();
        $confirma2 = Usuario::where('telefone', $telefone)->first();
        if ($confirma1 || $confirma2) {
            return redirect('/cadastro')->with('mensagem_erro', "Esse Email ou Telefone já esta cadastrado, use outro");
        }

        DB::table('usuario')->insert([
            'nome' => $nome,
            'email' => $email,
            'telefone' => $telefone,
            'senha' => $senha
        ]);

        return redirect('/login');
    }


    public function contaView()
    {
        $logado = Session::get('info_usuario');



        if (!$logado) {
            return redirect('/login')->with('mensagem_erro', "A pagina só é permitida para usuarios Logados");
        }



        return view('log/conta', [
            'logado' => $logado,

        ]);
    }

    public function conta(Request $request)
    {

        $logado = Session::get('info_usuario');


        $nomenovo = $request->input('nome_novo');
        $telefonenovo = $request->input('tele_novo');
        $emailnovo = $request->input('email_novo');
        $senhanovo = $request->input('senha_novo');


        DB::table('usuario')
            ->where('id', $logado->id)
            ->update([
                'nome' => $nomenovo,
                'email' => $emailnovo,
                'telefone' => $telefonenovo,
                'senha' => $senhanovo
            ]);

        /* Atu7alizar os dados na sessão */
        return redirect('/');
    }

    public function listar_imoveis()
    {
        $logado = Session::get('info_usuario');

        $id = $logado->id;

        $imoveis = DB::table('imovel')
            ->where('id_usuario', $id)
            ->get();

        return view('log/conta', [
            'logado' => $logado,
            'imoveis' => $imoveis,
        ]);
    }

    public function deletar_imovel(Request $request)
    {
        $logado = Session::get('info_usuario');

        $id = $request->input('id_imovel');

        DB::table('imovel')->where('id', '=', $id)->where('id_usuario', '=', $logado->id)->delete();
    }

    public function alterar_Imovel(Request $request)
    {
        $logado = Session::get('info_usuario');


        $quarto_novo = $request->input('quarto');
        $banheiro_novo = $request->input('banheiro');
        $preco_novo = $request->input('preco');
        $estado_novo = $request->input('estado');
        $cidade_novo = $request->input('cidade');
        $rua_novo = $request->input('rua');
        $bairro_novo = $request->input('bairro');
        $numero_novo = $request->input('numero');
        $cep_novo = $request->input('cep');
        $descricao_novo = $request->input('descricao');
        $id = $logado->id;


        DB::table('usuario')
            ->where('id', $logado->id)
            ->update([
                'quarto' => $quarto_novo,
                'banheiro' => $banheiro_novo,
                'preco' => $preco_novo,
                'estado' => $estado_novo,
                'cidade' => $cidade_novo,
                'rua' => $rua_novo,
                'bairro' => $bairro_novo,
                'numero' => $numero_novo,
                'cep' => $cep_novo,
                'descricao' => $descricao_novo
            ]);

            return redirect('/conta');

    }

    public function cadastro_Imovel(Request $request)
    {
        $logado = Session::get('info_usuario');

        $quarto = $request->input('quarto');
        $banheiro = $request->input('banheiro');
        $preco = $request->input('preco');
        $estado = $request->input('estado');
        $cidade = $request->input('cidade');
        $rua = $request->input('rua');
        $bairro = $request->input('bairro');
        $numero = $request->input('numero');
        $cep = $request->input('cep');
        $descricao = $request->input('descricao');
        $id = $logado->id;


        $encontrar = Imovel::where('numero', $numero)->where('rua', $rua)->where('cidade', $cidade)->where('estado', $estado)->first();

        if ($encontrar) {
            return redirect('/conta')->with('mensagem_erro', "Este imovel ja esta em anuncio");
        }


        DB::table('imovel')->insert([
            'quarto' => $quarto,
            'banheiro' => $banheiro,
            'preco' => $preco,
            'estado' => $estado,
            'cidade' => $cidade,
            'rua' => $rua,
            'bairro' => $bairro,
            'numero' => $numero,
            'cep' => $cep,
            'descricao' => $descricao,
            'id_usuario' => $id
        ]);
    }
}
