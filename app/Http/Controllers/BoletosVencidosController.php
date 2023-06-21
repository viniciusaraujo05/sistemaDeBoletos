<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use App\Models\BoletosVencidosModel;

/**
 * Class BoletoController
 */
class BoletosVencidosController extends Controller
{
    /**
     * @throws Exception
     */
    public function index(Request $request): array
    {
        $response = $this->getBoletoResponse($request);

        $paymentDate = $request->input('payment_date');

        $validaBoleto = $this->validaBoleto($response, $paymentDate);
        if($validaBoleto) {
            return $validaBoleto;
        }

        $calculoBoleto = $this->calculaBoleto($response, $paymentDate);
        $boleto =
            [
                "original_amount"=> $response['amount'],
                "amount"=> $calculoBoleto['amount'],
                "due_date" => $response['due_date'],
                "payment_date" => $paymentDate,
                "interest_amount_calculated" => $calculoBoleto['interest'],
                "fine_amount_calculated" =>  $calculoBoleto['fine']
            ];

        $this->saveBoleto($boleto);

        return $boleto;
    }

    /**
     * GetBoletoResponse method
     *
     * @param Request $request
     *
     * @return PromiseInterface|Response
     */
    public function getBoletoResponse(Request $request): PromiseInterface|Response
    {
        $url = 'https://vagas.builders/api/builders/bill-payments/codes';
        $payload = [
            'code' =>  $request->input('bar_code'),
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => $request->headers->get('Authorization'),
        ])->post($url, $payload);

        return $response;
    }

    /**
     * ValidaBoleto method
     *
     * @param Response $response
     *
     * @return array|string[]
     */
    public function validaBoleto(Response $response, String $paymentDate): array
    {
        if ($response->failed() || $response->serverError()) {
            $message = $response->status() == 401 ?
                ['error' => 'TOKEN DE AUTORIZAÇÃO INVALIDO'] : ['error' => 'CARTÃO INVALIDO'];
            return $message;
        }

        if ($response['due_date'] >= $paymentDate) {
            return ['error' => 'ESTE BOLETO AINDA NÃO ESTÁ VENCIDO'];
        }

        if ($response['type'] != 'NPC') {
            return ['error' => 'APENAS BOLETOS DO TIPO NPC SÃO ACEITOS'];
        }

        return [];
    }

    /**]
     * CalculaBoleto method
     *
     * @param Response $response
     * @param String $paymentDate
     * @return float[]
     */
    public function calculaBoleto(Response $response, String $paymentDate): array
    {
        $data = Carbon::parse($response['due_date']);
        $diasAtraso = $data->diff($paymentDate)->days;

        $taxa_juros = 0.00033;
        $multa = 0.0002;

        $interest = $response['amount'] * $taxa_juros * $diasAtraso;
        $fine = $response['amount'] * $multa;
        $total = $response['amount'] + $interest + $fine;

        return [
            'interest' => (float)number_format($interest, 2),
            'fine' => (float)number_format($fine, 2),
            'amount' => (float)number_format($total, 2)
        ];
    }

    /**
     * SaveBoleto method
     *
     * @param array $boleto
     *
     * @return void
     */
    public function saveBoleto(array $boleto): void
    {
       BoletosVencidosModel::firstOrCreate(
            [
                'amount' => $boleto['amount'],
                'original_amount' => $boleto['original_amount'],
                'due_date' => $boleto['due_date'],
                'payment_date' => $boleto['payment_date']
            ],
            $boleto
        );
    }
}
