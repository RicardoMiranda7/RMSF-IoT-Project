package com.example.jose_trabalho.myapplication;

import android.support.v7.app.AppCompatActivity;
import android.os.Bundle;
import android.widget.*;
import java.util.List;
import java.util.ArrayList;
import java.util.HashMap;
import android.view.View;
import android.widget.Toast;
import com.example.jose_trabalho.myapplication.ExpandableListAdapter;

// Thanks to Ravi Tamada for the tutorial

public class Records extends AppCompatActivity {
    ExpandableListAdapter listAdapter;
    ExpandableListView expListView;
    List<String> listDataHeader;
    HashMap<String, List<String>> listDataChild;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_records);

        // get the listview
        expListView = (ExpandableListView) findViewById(R.id.lvExp);

        // preparing list data
        prepareListData();

        listAdapter = new ExpandableListAdapter(this, listDataHeader, listDataChild);

        // setting list adapter
        expListView.setAdapter(listAdapter);


        // Listview Group expanded listener
        expListView.setOnGroupExpandListener(new ExpandableListView.OnGroupExpandListener() {

            @Override
            public void onGroupExpand(int groupPosition) {
                Toast.makeText(getApplicationContext(),
                        listDataHeader.get(groupPosition) + " Expanded",
                        Toast.LENGTH_SHORT).show();
            }
        });
        // Listview Group collasped listener
        expListView.setOnGroupCollapseListener(new ExpandableListView.OnGroupCollapseListener() {

            @Override
            public void onGroupCollapse(int groupPosition) {
                Toast.makeText(getApplicationContext(),
                        listDataHeader.get(groupPosition) + " Collapsed",
                        Toast.LENGTH_SHORT).show();

            }
        });
        // Listview on child click listener
        expListView.setOnChildClickListener(new ExpandableListView.OnChildClickListener() {

            @Override
            public boolean onChildClick(ExpandableListView parent, View v,
                                        int groupPosition, int childPosition, long id) {
                Toast.makeText(
                        getApplicationContext(),
                        listDataHeader.get(groupPosition)
                                + " : "
                                + listDataChild.get(
                                listDataHeader.get(groupPosition)).get(
                                childPosition), Toast.LENGTH_SHORT)
                        .show();
                return false;
            }
        });
    }

    /*
     * Preparing the list data
     */
    private void prepareListData() {
        listDataHeader = new ArrayList<String>();
        listDataChild = new HashMap<String, List<String>>();

        // Adding child data
        listDataHeader.add("PAN1");
        listDataHeader.add("PAN2");
        listDataHeader.add("PAN3");

        // Adding child data
        List<String> PAN1 = new ArrayList<String>();
        PAN1.add("Sensor 1");
        PAN1.add("Sensor 2");
        PAN1.add("Sensor 3");
        PAN1.add("Sensor 4");

        List<String> PAN2 = new ArrayList<String>();
        PAN2.add("Sensor 1");
        PAN2.add("Sensor 2");

        List<String> PAN3 = new ArrayList<String>();
        PAN3.add("Sensor 1");

        listDataChild.put(listDataHeader.get(0), PAN1); // Header, Child data
        listDataChild.put(listDataHeader.get(1), PAN2);
        listDataChild.put(listDataHeader.get(2), PAN3);
    }

}
