package com.example.jose_trabalho.myapplication;

import android.content.Intent;
import android.graphics.Color;
import android.hardware.Sensor;
import android.net.Uri;
import android.os.AsyncTask;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Adapter;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.SimpleAdapter;
import android.widget.Switch;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.ToggleButton;

import com.google.android.gms.appindexing.Action;
import com.google.android.gms.appindexing.AppIndex;
import com.google.android.gms.common.api.GoogleApiClient;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.concurrent.TimeUnit;

public class Settings extends AppCompatActivity {



    class Sensor {
        String ID = "";
        boolean enabled;

    }

    // Array of booleans to store toggle button status
    public boolean Buzzer;
    public boolean Propagation;
    public boolean AlarmSysEnabled;
    String PANid = "20";
    String ServerIP;
    Switch swBuzzer;
    Switch swPropagation;
    int sensorsLen;
    View selectedListItem;
    Sensor[] sensors = new Sensor[10];


    public void toggleBuzzer(View view) {
        Buzzer = !Buzzer;
        new ModifyTask().execute(PANid, "BUZZER", String.valueOf(Buzzer),"GenSettings");
    }

    public void toggleEnableAlarm(View view) {
        AlarmSysEnabled = !AlarmSysEnabled;
        new ModifyTask().execute(PANid, "ENABLE", String.valueOf(AlarmSysEnabled), "GenSettings");
    }

    public void togglePropagation(View view) {
        Propagation = !Propagation;
        new ModifyTask().execute(PANid, "PROPAGATION", String.valueOf(Propagation), "GenSettings");
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_settings);

        Bundle extras = getIntent().getExtras();
        if (extras != null) {
            ServerIP = extras.getString("ServerIP");
        }

        for (int i = 0; i < sensors.length; i++) {
            sensors[i] = new Sensor();
        }

        Toast.makeText(getApplicationContext(), "Loading current settings from " + ServerIP, Toast.LENGTH_SHORT).show();

        ToggleButton swBuzzer = (ToggleButton) findViewById(R.id.swBuzzer);
        ToggleButton swPropagation = (ToggleButton) findViewById(R.id.swPropagation);
        ToggleButton swAlarmEnable = (ToggleButton) findViewById(R.id.swEnableAlarm);
        //Verificar no servidor e na BD quais as configuracoes actuais

        new GetSettingsTask().execute(PANid);
        try {
            TimeUnit.SECONDS.sleep(2);
        } catch (InterruptedException e) {
            // TODO Auto-generated catch block
            e.printStackTrace();
        }

        for (int i = 0; i < sensors.length; i++) {

            if ((sensors[i].ID.compareTo("")) == 0) {
                sensorsLen = (i);
                break;
            }
        }
        for (Sensor s : sensors) {
            Log.d("Sensor: ", s.ID);
            Log.d("Sensor: ", String.valueOf(s.enabled));

        }


        //Passa-las para a interface gráfica
        swAlarmEnable.setChecked(AlarmSysEnabled);
        swBuzzer.setChecked(Buzzer);
        Log.d("Buzzer2: ", String.valueOf(Buzzer));
        swPropagation.setChecked(Propagation);

        Log.d("Sensors length: ", String.valueOf(sensorsLen));
        String[] sSensors = new String[sensorsLen];
        ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, sSensors);
        ListView lvSensors = (ListView) findViewById(R.id.lvSensors);
        lvSensors.setAdapter(adapter);


        for (int i = 0; i < sensorsLen; i++) {
            Log.d("i:", String.valueOf(i));
            sSensors[i] = "ID: " + sensors[i].ID + "                      Enabled: " + String.valueOf(sensors[i].enabled);

          /* if (sensors[i].enabled == true) {
                lvSensors.getChildAt(lvSensors.getFirstVisiblePosition()).setBackgroundColor(0x458B00);

            } else {
                lvSensors.getChildAt(i - (lvSensors.getFirstVisiblePosition() - lvSensors.getHeaderViewsCount())).setBackgroundColor(Color.WHITE);

            }*/
        }
        //lvSensors.setBackgroundColor(Color.WHITE);

        // Toast.makeText(getApplicationContext(), "Buzzer: " + Buzzer + "     Propagation: " + Propagation, Toast.LENGTH_SHORT).show();


        lvSensors.setOnItemClickListener(new OnItemClickListener() {

            public void onItemClick(AdapterView adapterView, View view, int position, long id) {
                sensors[position].enabled = !sensors[position].enabled;
                String msg;
                if(sensors[position].enabled) msg = "Sensor ENABLED";
                else msg = "Sensor DISABLED";
                Toast.makeText(getApplicationContext(), msg, Toast.LENGTH_SHORT).show();
                if (sensors[position].enabled == true) {
                    adapterView.getChildAt(position).setBackgroundColor(Color.parseColor("#458B00"));
                } else {
                    adapterView.getChildAt(position).setBackgroundColor(Color.WHITE);
                }

                Log.d("Position: ", String.valueOf(position));
                Log.d("ID: ", String.valueOf(id));
                new ModifyTask().execute(PANid, String.valueOf(sensors[position].enabled ? 1 : 0),String.valueOf(sensors[position].ID), "Sensor");

            }
        });
        adapter.notifyDataSetChanged();
        lvSensors.setAdapter(adapter);

    }

    private class ModifyTask extends AsyncTask<String, Void, String> {

        @Override
        protected String doInBackground(String... strings) {

            //Clique no botao de Registo
            String message;
            // Pedido à base de dados
            ClientJava clientRegister = new ClientJava(ServerIP, new IPandPORT().PHPServer_Port);
            switch (strings[3]) {
                case "GenSettings":
                    clientRegister.send_message("JAVA MODIFY " + strings[0] + " " + strings[1] + " " + (Boolean.parseBoolean(strings[2]) ? 1 : 0) + "\n");
                    break;
                case "Sensor":
                    clientRegister.send_message("JAVA MODIFY " + strings[0] + " SENSOR " + strings[1] +" " + strings[2] + "\n");
                    break;
            }
            message = clientRegister.receive_message();
            Log.d("Message rcv: ", message);
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
            clientRegister.send_message("JAVA RETRIEVE " + strings[0] + "\n");
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
                    AlarmSysEnabled = Integer.parseInt(parts[1]) != 0;
                    Buzzer = Integer.parseInt(parts[2]) != 0;
                    Propagation = Integer.parseInt(parts[3]) != 0;

                    int j = 0;
                    for (int i = 4; i < parts.length-1; i+=2) {
                        sensors[j].ID = parts[i];
                        sensors[j].enabled = Integer.parseInt(parts[i + 1]) != 0;
                        j++;
                    }
                    Log.d("message: ", message);
                    Log.d("Alarm enable: ", String.valueOf(AlarmSysEnabled));
                    Log.d("buzzer: ", String.valueOf(Buzzer));
                    Log.d("Propagation: ", String.valueOf(Propagation));

                }
            }
            return "";

        }

        protected void onPostExecute(String UserFeedback) {

        }
    }


}
