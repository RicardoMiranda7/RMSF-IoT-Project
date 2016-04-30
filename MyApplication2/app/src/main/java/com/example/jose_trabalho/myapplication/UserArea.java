package com.example.jose_trabalho.myapplication;

import android.content.Intent;
import android.os.Handler;
import android.os.Looper;
import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ListView;

public class UserArea extends AppCompatActivity {
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
        setContentView(R.layout.activity_user_area);

        //Login Fields
        final EditText etEmail = (EditText) findViewById(R.id.etEmail);
        final EditText etPassword = (EditText) findViewById(R.id.etPassword);



        String[] sActivities = new String[]{"Settings", "Records"};

        ArrayAdapter<String> adapterAct = new ArrayAdapter<String>(this, R.layout.listview, sActivities);
        ListView lvActivities = (ListView) findViewById(R.id.lvActivities);
        lvActivities.setAdapter(adapterAct);
        lvActivities.setOnItemClickListener(GoToActivities());

    }
    public AdapterView.OnItemClickListener GoToActivities(){
        return(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
                Intent intent;

                switch (position) {
                    case 0:
                       intent = new Intent(UserArea.this, Settings.class);
                        startActivity(intent);
                        break;
                    case 1:
                        intent = new Intent(UserArea.this, Records.class);
                        startActivity(intent);
                        break;


                }
            }
        });
    }



    public void startService(View view)
    {
        Intent intent =  new Intent(this,MyService.class);
        startService(intent);
    }

    public void stopService(View view) {
        Intent intent =  new Intent(this,MyService.class);
        stopService(intent);
    }
}
