package com.example.jose_trabalho.myapplication;

import android.content.Intent;
import android.os.AsyncTask;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.SimpleAdapter;
import android.widget.Switch;
import android.widget.Toast;
import android.widget.ToggleButton;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.concurrent.TimeUnit;

public class Settings extends AppCompatActivity {

    // Array of booleans to store toggle button status
    public boolean Buzzer;
    public boolean Propagation;
    public boolean AlarmSysEnabled;
    String PANid = "20";
    String ServerIP;
    Switch swBuzzer ;
    Switch swPropagation;

    public void toggleBuzzer(View view){
       Buzzer = !Buzzer;
        new ModifyTask().execute(PANid,"BUZZER", String.valueOf(Buzzer));
    }

    public void toggleEnableAlarm(View view){
        AlarmSysEnabled = !AlarmSysEnabled;
        new ModifyTask().execute(PANid,"ENABLE", String.valueOf(AlarmSysEnabled));
    }
    public void togglePropagation(View view){
        Propagation = !Propagation;
        new ModifyTask().execute(PANid, "PROPAGATION", String.valueOf(Propagation));
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_settings);

        Bundle extras = getIntent().getExtras();
        if (extras != null) {
            ServerIP = extras.getString("ServerIP");
        }

        String[] sSensors = new String[]{"Leaving Room", "Room", "Hall"};

        ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, sSensors);
        ListView lvSensors = (ListView) findViewById(R.id.lvSensors);
        lvSensors.setAdapter(adapter);

        Toast.makeText(getApplicationContext(), "Loading current settings from " + ServerIP, Toast.LENGTH_SHORT).show();

        ToggleButton swBuzzer = (ToggleButton) findViewById(R.id.swBuzzer) ;
        ToggleButton swPropagation = (ToggleButton) findViewById(R.id.swPropagation);
        ToggleButton swAlarmEnable = (ToggleButton) findViewById(R.id.swEnableAlarm);
        //Verificar no servidor e na BD quais as configuracoes actuais

        new GetSettingsTask().execute(PANid);
        try {
            TimeUnit.SECONDS.sleep(1);
        } catch (InterruptedException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }
        //Passa-las para a interface gráfica
        swAlarmEnable.setChecked(Buzzer);
        swBuzzer.setChecked(Buzzer);
        Log.d("Buzzer2: ", String.valueOf(Buzzer));
        swPropagation.setChecked(Propagation);

       // Toast.makeText(getApplicationContext(), "Buzzer: " + Buzzer + "     Propagation: " + Propagation, Toast.LENGTH_SHORT).show();
    }

    private class ModifyTask extends AsyncTask<String, Void, String> {

        @Override
        protected String doInBackground(String... strings) {

            //Clique no botao de Registo
            String message;
            // Pedido à base de dados
            ClientJava clientRegister = new ClientJava(ServerIP, new IPandPORT().PHPServer_Port);
            clientRegister.send_message("JAVA MODIFY " + strings[0] + " " + strings[1] + " " + (Boolean.parseBoolean(strings[2]) ? 1 : 0) + "\n");
            message = clientRegister.receive_message();

            try {
                clientRegister.Close();
            } catch (IOException e) {
                e.printStackTrace();
            }
            //message = "NOK PASSWD";
            //Se a comunicacao c/base de dados for bem sucedida
            if (message != null) {
                if (message.regionMatches(0, "OK", 0, 2)) {
                    return strings[0] + " modified";
                } else return "Error ocurred";
            }
            return "Failed communication with server";
        }

        protected void onPostExecute(String UserFeedback) {
            //Toast.makeText(getApplicationContext(), UserFeedback, Toast.LENGTH_SHORT).show();
        }
    }

    private class GetSettingsTask extends AsyncTask<String, Void, String> {

        @Override
        protected String doInBackground(String... strings) {
            String message;
            // Pedido à base de dados
            ClientJava clientRegister = new ClientJava(ServerIP, new IPandPORT().PHPServer_Port);
            // strings[0] = PAN ID
            clientRegister.send_message("JAVA RETRIEVE " + strings[0] + " ENABLE BUZZER PROPAGATION\n");
            message = clientRegister.receive_message();
            try {
                clientRegister.Close();
            } catch (IOException e) {
                e.printStackTrace();
            }
            //message = "NOK PASSWD";
            //Se a comunicacao c/base de dados for bem sucedida
            if (message != null) {
                if (message.regionMatches(0, "OK", 0, 2)) {
                    String[] parts = message.split(" ");
                    AlarmSysEnabled = Integer.parseInt(parts[1])!= 0;
                    Buzzer = Integer.parseInt(parts[2]) != 0;
                    Propagation = Integer.parseInt(parts[3]) != 0;

                    Log.d("message: ",message);
                    Log.d("Alarm enable: ",String.valueOf( AlarmSysEnabled));
                    Log.d("buzzer: ",String.valueOf(Buzzer));
                    Log.d("Propagation: ",String.valueOf(Propagation));

                }
            }
            return "";

        }

        protected void onPostExecute(String UserFeedback) {

        }
    }





}
