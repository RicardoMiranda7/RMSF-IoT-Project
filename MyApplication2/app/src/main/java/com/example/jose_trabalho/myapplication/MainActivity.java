package com.example.jose_trabalho.myapplication;

import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.support.v7.app.AppCompatActivity;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;

import junit.framework.Test;

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

        bSignIn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent registerAdminIntent = new Intent(MainActivity.this, UserArea.class);
                MainActivity.this.startActivity(registerAdminIntent);
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




}
