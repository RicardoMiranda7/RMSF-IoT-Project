package com.example.jose_trabalho.myapplication;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.ArrayAdapter;
import android.widget.ListView;
import android.widget.RelativeLayout;
import android.widget.SimpleAdapter;
import android.widget.Toast;
import android.widget.ToggleButton;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

public class Settings extends AppCompatActivity {

    // Array of booleans to store toggle button status
    public boolean[] status = {
            true,
            false,
            false,
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_settings);
        String[] sSensors = new String[]{"Leaving Room", "Room", "Hall"};


        ArrayAdapter<String> adapter = new ArrayAdapter<String>(this, android.R.layout.simple_list_item_1, sSensors);
        ListView lvSensors = (ListView) findViewById(R.id.lvSensors);
        lvSensors.setAdapter(adapter);
    }



}
