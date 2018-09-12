<?php

namespace App\Libraries;

use App\Libraries\FPDF;
use URL;

class ClaimReportPdf extends FPDF {

    function Header($header) {
        $this->SetAuthor("BuscarV Soluciones Tecnológicas");
        $this->SetTitle('Claim');
        $this->SetFont('Arial', 'B', 12);
        $this->SetXY(85, 3);
        $this->Cell(35, 8, utf8_decode("Formato de reclamación de daños y/o faltantes"), 0, 0, 'C', 0);
        $this->Image("http://localhost:8888/kdem/public/img/glovis.png", 10, 15, 50, 18);
        $this->SetFont('Arial', 'B', 8);
        $this->SetXY(130, 15);
        $this->SetFillColor(37, 108, 172);
        $this->SetTextColor(255);
        $this->SetDrawColor(45, 65, 97);
        $this->Cell(35, 8, utf8_decode("Empresa transportista"), 1, 0, 'C', 1);
        $this->SetTextColor(0, 0, 0);
        $this->SetXY(165, 15);
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 8, utf8_decode($header["carrier"]), 1, 1, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->SetXY(130, 23);
        $this->SetFillColor(37, 108, 172);
        $this->SetTextColor(255);
        $this->SetDrawColor(45, 65, 97);
        $this->Cell(35, 10, " ", 1, 0, 'L', 1);
        $this->SetXY(132, 24);
        $this->Cell(30, 4, utf8_decode("Fecha y hora de"), 0, 1, 'L', 0);
        $this->SetXY(132, 28);
        $this->Cell(30, 4, utf8_decode("recepción del vehiculo"), 0, 1, 'L', 0);
        $this->SetXY(165, 23);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 8);
        $this->Cell(40, 10, $header["arrive_date"], 1, 1, 'C');
    }

    function dealerInfo($claim) {
        $y = $this->GetY();
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(255);
        $this->SetDrawColor(45, 65, 97);
        $this->SetLineWidth(0);
        $this->setXY(10, $y + 10);
        $this->Cell(195, 6, utf8_decode("Datos del concesionario"), 1, 0, 'C', 1);
        $this->setXY(10, $y + 16);
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(70, 5, utf8_decode("Nombre del taller"), 1, 0, 'C', 1);
        $this->setXY(80, $y + 16);
        $this->SetFont('Arial', '', 8);
        $this->Cell(125, 5, utf8_decode($claim->dealer->commercial_name), 1, 0, 'C', 0);
        $this->setXY(10, $y + 21);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(70, 5, utf8_decode("Razón social"), 1, 0, 'C', 1);
        $this->setXY(80, $y + 21);
        $this->SetFont('Arial', '', 8);
        $this->Cell(125, 5, utf8_decode($claim->dealer->business_name), 1, 0, 'C', 0);
        $this->setXY(10, $y + 26);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(70, 5, utf8_decode("RFC"), 1, 0, 'C', 1);
        $this->setXY(80, $y + 26);
        $this->SetFont('Arial', '', 8);
        $this->Cell(125, 5, utf8_decode($claim->dealer->rfc), 1, 0, 'C', 0);
        $this->setXY(10, $y + 31);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(70, 5, utf8_decode("Nombre del responsable"), 1, 0, 'C', 1);
        $this->setXY(80, $y + 31);
        $this->SetFont('Arial', '', 8);
        $this->Cell(125, 5, utf8_decode($claim->responsable_name), 1, 0, 'C', 0);
        $this->setXY(10, $y + 36);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(70, 5, utf8_decode("Teléfono"), 1, 0, 'C', 1);
        $this->setXY(80, $y + 36);
        $this->SetFont('Arial', '', 8);
        $this->Cell(125, 5, utf8_decode($claim->responsable_phone), 1, 0, 'C', 0);
        $this->setXY(10, $y + 41);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(70, 5, utf8_decode("E-Mail"), 1, 0, 'C', 1);
        $this->setXY(80, $y + 41);
        $this->SetFont('Arial', '', 8);
        $this->Cell(125, 5, utf8_decode($claim->responsable_email), 1, 0, 'C', 0);
    }

    function carInfo($carInfo) {
        $y = $this->GetY();
        $this->setXY(10, $y + 10);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(255);
        $this->SetDrawColor(45, 65, 97);
        $this->SetLineWidth(0);
        $this->SetFillColor(37, 108, 172);
        $this->Cell(195, 6, utf8_decode("Datos del vehiculo"), 1, 0, 'C', 1);
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0, 0, 0);
        $this->setXY(10, $y + 16);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(48.75, 5, utf8_decode("VIN"), 1, 0, 'C', 1);
        $this->setXY(58.75, $y + 16);
        $this->SetFont('Arial', '', 8);
        $this->Cell(48.75, 5, utf8_decode($carInfo["vin"]), 1, 0, 'C', 0);
        $this->setXY(107.5, $y + 16);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(48.75, 5, utf8_decode("Modelo"), 1, 0, 'C', 1);
        $this->setXY(156.25, $y + 16);
        $this->SetFont('Arial', '', 8);
        $this->Cell(48.75, 5, utf8_decode($carInfo["model"]), 1, 0, 'C', 0);
    }

    function damages($damages) {
        $i = 0;
        $y = $this->GetY();
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(255);
        $this->SetDrawColor(45, 65, 97);
        $this->SetLineWidth(0);
        $this->SetFillColor(37, 108, 172);
        $this->setXY(10, $y + 10);
        $this->Cell(195, 6, utf8_decode("Descripción de daños"), 1, 0, 'C', 1);
        $this->SetFillColor(224, 235, 255);
        $this->setXY(10, $y + 16);
        $y = $this->GetY();
        $this->SetTextColor(0, 0, 0);
        $this->Cell(15, 5, utf8_decode("No. Daño"), 1, 0, 'C', 1);
        $this->setXY(25, $y);
        $this->Cell(60, 5, utf8_decode("Area Dañada"), 1, 0, 'C', 1);
        $this->setXY(85, $y);
        $this->Cell(60, 5, utf8_decode("Tipo de daño"), 1, 0, 'C', 1);
        $this->setXY(145, $y);
        $this->Cell(60, 5, utf8_decode("Severidad del daño"), 1, 0, 'C', 1);
        $this->SetFont('Arial', '', 7);
        $this->setXY(10, $this->GetY());
        $this->setXY(10, $y + 5);

        foreach ($damages as $damage) {
            $this->Cell(15, 5, utf8_decode(($i + 1)), 1, 0, 'C', 0);
            $this->Cell(60, 5, utf8_decode($damage->damageArea->number . " - " . $damage->damageArea->name), 1, 0, 'L', 0);
            $this->Cell(60, 5, utf8_decode($damage->damage->number . " - " . $damage->damage->name), 1, 0, 'L', 0);
            $this->Cell(60, 5, utf8_decode($damage->damageSeverity->number . " - " . $damage->damageSeverity->name), 1, 0, 'L', 0);
            $this->Ln();
            $i = $i + 1;
        }
    }

    function quotations($damages) {
        $i = 0;
        $y = $this->GetY();
        $granTotal = 0;
        $this->setXY(10, $y + 5);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(255);
        $this->SetDrawColor(45, 65, 97);
        $this->SetLineWidth(0);
        $this->SetFillColor(37, 108, 172);
        $this->Cell(195, 6, utf8_decode("Cotización de reparación"), 1, 0, 'C', 1);
        $this->setXY(10, $y + 13);

        foreach ($damages as $damage) {
            if ($i != 0) {
                $this->Ln();
            }
            $this->SetFillColor(224, 235, 255);
            $this->SetTextColor(0, 0, 0);
            $this->SetFont('Arial', 'B', 8);
            $y = $this->GetY();
            //Headers
            $this->Cell(15, 5, utf8_decode("No. Daño"), 1, 0, 'C', 1);
            $this->Cell(60, 5, utf8_decode("Costo refacciones"), 1, 0, 'C', 1);
            $this->Cell(60, 5, utf8_decode("Costo pintura"), 1, 0, 'C', 1);
            $this->Cell(60, 5, utf8_decode("Costo mano de obra"), 1, 0, 'C', 1);
            $this->Ln();
            $this->SetFont('Arial', '', 8);
            $this->Cell(15, 5, utf8_decode(($i + 1)), 1, 0, 'C', 0);
            $this->Cell(60, 5, utf8_decode("$ " . $damage->damageQuotation->amount_pieces), 1, 0, 'C', 0);
            $this->Cell(60, 5, utf8_decode("$ " . $damage->damageQuotation->amount_paint), 1, 0, 'C', 0);
            $this->Cell(60, 5, utf8_decode("$ " . $damage->damageQuotation->amount_hand), 1, 0, 'C', 0);
            //Subtotal
            $this->Ln();
            $this->setXY(85, $this->GetY());
            $this->Cell(60, 5, utf8_decode("Subtotal"), 1, 0, 'C', 1);
            $this->Cell(60, 5, utf8_decode("$ " . $damage->damageQuotation->subtotal), 1, 0, 'C', 0);
            //IVA
            $this->Ln();
            $this->setXY(85, $this->GetY());
            $this->Cell(60, 5, utf8_decode("Trabajo por reparación"), 1, 0, 'C', 1);
            $this->Cell(60, 5, utf8_decode("$ " . $damage->damageQuotation->iva), 1, 0, 'C', 0);
            //Total
            $this->Ln();
            $this->setXY(85, $this->GetY());
            $this->Cell(60, 5, utf8_decode("Total"), 1, 0, 'C', 1);
            $this->SetFillColor(37, 108, 172);
            $this->SetFont('Arial', 'B', 8);
            $this->SetTextColor(255);
            $this->Cell(60, 5, utf8_decode("$ " . $damage->damageQuotation->total), 1, 0, 'C', 1);
            $this->Ln();
            $granTotal = $granTotal + $damage->damageQuotation->total;
            $i = $i + 1;
        }

        //Gran Total
        $this->Ln();
        $this->SetFillColor(224, 235, 255);
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(0, 0, 0);
        $this->setXY(85, $this->GetY());
        $this->Cell(60, 5, utf8_decode("Total"), 1, 0, 'C', 1);
        $this->SetFillColor(37, 108, 172);
        $this->SetTextColor(255);
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(60, 5, utf8_decode("$ " . $granTotal), 1, 0, 'C', 1);
    }

    function photos($photos) {
        $i = 0;
        $y = $this->GetY();
        $this->setXY(10, $y + 10);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(255);
        $this->SetDrawColor(45, 65, 97);
        $this->SetLineWidth(0);
        $this->SetFillColor(37, 108, 172);
        $this->Cell(195, 6, utf8_decode("Fotografías"), 1, 0, 'C', 1);
        $countLn = 0;
        $this->setXY(10, $this->GetY() + 6);

        foreach ($photos as $photo) {
            if ($countLn == 3) {
                $countLn = 0;
                $this->Ln();
                $this->Cell(65, 50, $this->Image("http://http://localhost:8888/kdem/public/storage/" . $photo->src_pic, $this->GetX(), $this->GetY(), 65, 50), 1, 0, 'L', 0);
            } else {
                $this->Cell(65, 50, $this->Image("http://localhost:8888/kdem/public/storage/" . $photo->src_pic, $this->GetX(), $this->GetY(), 65, 50), 1, 0, 'L', 0);
            }
            $countLn = $countLn + 1;
            $i = $i + 1;
        }
    }

    function Footer() {

        $this->SetY(-10);

        $this->SetFont('Arial', 'I', 8);

        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/' . $this->PageNo(), 0, 0, 'C');
    }

}
