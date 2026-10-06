@extends('errors.layout')

@section('code', '403')
@section('title', 'Accesso negato')
@section('message', $exception->getMessage() ?: 'Non hai i permessi per visualizzare questa pagina.')