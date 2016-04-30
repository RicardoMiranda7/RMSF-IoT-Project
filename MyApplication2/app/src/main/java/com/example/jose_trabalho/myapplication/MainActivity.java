package com.example.jose_trabalho.myapplication;

import android.app.Activity;
import android.content.Intent;
import android.graphics.Bitmap;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.TextView;
import android.widget.Toast;

import junit.framework.Test;

import java.io.IOException;
import java.net.PasswordAuthentication;

public class MainActivity extends AppCompatActivity {

    public static Handler UIHandler;

    static
    {
        UIHandler = new Handler(Looper.getMainLooper());
    }
    public static void runOnUI(Runnable runnable) {
        UIHandler.post(runnable);
    }


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.content_main);

        //Login Fields
        final EditText etEmail = (EditText) findViewById(R.id.etEmail);
        final EditText etPassword = (EditText) findViewById(R.id.etPassword);
        //Buttons
        final Button bSignIn = (Button) findViewById(R.id.bSignIn);
        final Button bRegister = (Button) findViewById(R.id.bRegister);
        //Text View Erros
        final TextView tvEmailError = (TextView) findViewById(R.id.tvEmailError);
        final TextView tvPasswError = (TextView) findViewById(R.id.tvPasswError);

        bSignIn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                new LoginTask().execute(etEmail.getText().toString(), etPassword.getText().toString());
            }

        });
        etEmail.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                tvEmailError.setVisibility(View.INVISIBLE); // Esconder aviso
            }
        });

        etPassword.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                tvPasswError.setVisibility(View.INVISIBLE); // Esconder aviso
            }
        });


        bRegister.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent RegisterAdminIntent = new Intent(MainActivity.this, RegisterActivity.class);
                MainActivity.this.startActivity(RegisterAdminIntent);
            }
        });
    }



    private class LoginTask extends AsyncTask<String, Void, String> {

        @Override
        protected String doInBackground(String... strings) {

            //Clique no botao de Registo
            String message, Userfeedback;
            // Pedido à base de dados
            ClientJava clientRegister = new ClientJava(new IPandPORT().PHPServer_IP, new IPandPORT().PHPServer_Port);
            clientRegister.send_message("JAVA LOGIN " + " " + strings[0] + " " + strings[1] + "\n");
            message = clientRegister.receive_message();
            try {
                clientRegister.Close();
            } catch (IOException e) {
                e.printStackTrace();
            }
            //message = "NOK PASSWD";
            //Se a comunicacao c/base de dados for bem sucedida
            if(message!=null) {
                if (message.regionMatches(0, "OK", 0, 2)) {

                    return Userfeedback = "Login Successful!";

                } else if (message.regionMatches(0, "NOK EMAIL", 0, 9)) {
                    return Userfeedback = "Email adress not found.";
                } else if (message.regionMatches(0, "NOK PASSWD", 0, 10)) {
                    return Userfeedback = "Password does not match Email.";
                }
            }
            return "";
        }
        protected void onPostExecute(String UserFeedback) {

            Toast.makeText(getApplicationContext(), UserFeedback, Toast.LENGTH_SHORT).show();
            if (UserFeedback.equals("Login Successful!")){
                Intent UserAreaIntent = new Intent(MainActivity.this, UserArea.class);
                MainActivity.this.startActivity(UserAreaIntent);
            }

        }
    }


}




