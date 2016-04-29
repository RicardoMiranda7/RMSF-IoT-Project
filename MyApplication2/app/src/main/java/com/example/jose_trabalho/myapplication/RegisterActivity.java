package com.example.jose_trabalho.myapplication;

import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import java.net.ContentHandler;
import java.util.concurrent.TimeUnit;

public class RegisterActivity extends AppCompatActivity {
    private int PHPServer_Port = 1907;
    private String PHPServer_IP = "192.168.0.105";
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);
        //Administrator Fields
        final EditText etName = (EditText) findViewById(R.id.etName);
        final EditText etEmail = (EditText) findViewById(R.id.etEmail);
        //PAN Fields
        final EditText etPANid = (EditText) findViewById(R.id.etPANid);
        final EditText etPANsk = (EditText) findViewById(R.id.etPANsk);
        //Text filed para indicacoes de erro
        final TextView tvPANidError = (TextView) findViewById(R.id.tvPANidError);
        final TextView tvPANskError = (TextView) findViewById(R.id.tvPANskError);
        //Button Field
        final Button bRegisterAdmin = (Button) findViewById(R.id.bRegisterAdmin);
        //Clique no botao de Registo
        bRegisterAdmin.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
            String message;
           // Pedido à base de dados
           // ClientJava clientRegister = new ClientJava(PHPServer_IP,PHPServer_Port);
           // clientRegister.send_message("JAVA REG " + etName + " " + etEmail + " " + etPANid + " " + etPANsk + "\n");
           // message = clientRegister.receive_message();
                message = "NOK PANsk";
            //Se a comunicacao c/base de dados for bem sucedida
            if(message.regionMatches(0, "OK", 0, 2)){
                Toast.makeText(getApplicationContext(), "Register Successful!", Toast.LENGTH_SHORT).show();
                Toast.makeText(getApplicationContext(), "A password has been sent to the specified email to access the application.", Toast.LENGTH_LONG).show();
                Intent BackToLoginIntent = new Intent(RegisterActivity.this, MainActivity.class);
                RegisterActivity.this.startActivity(BackToLoginIntent);
            }else if(message.regionMatches(0, "NOK PANid", 0, 9)){
                tvPANidError.setVisibility(View.VISIBLE); // Mostrar aviso
                etPANid.setText("");
            }else if(message.regionMatches(0, "NOK PANsk", 0, 9)) {
                tvPANskError.setVisibility(View.VISIBLE); // Mostrar aviso
                etPANsk.setText("");
            }
        }

        });

        etPANid.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                tvPANidError.setVisibility(View.INVISIBLE); // Esconder aviso
            }
        });

        etPANsk.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                tvPANskError.setVisibility(View.INVISIBLE); // Esconder aviso
            }
        });
    }


}
