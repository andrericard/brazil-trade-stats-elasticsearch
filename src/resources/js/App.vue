<template>
  <v-app full-height="false">
    <v-app-bar color="purple-darken-1" title="Brazil Trade Stats"></v-app-bar>
    <v-navigation-drawer>
      <v-list>
        <v-list-item>
          <v-combobox
            chips
            v-model="filters.ncm_code"
            closable-chips
            color="purple-darken-1"
            label="NCM"
            multiple
          ></v-combobox>
        </v-list-item>
        <v-list-item>
          <v-combobox
            :items="estados"
            chips
            v-model="filters.importer_state"
            closable-chips
            color="purple-darken-1"
            label="UF Importador"
            multiple
          ></v-combobox>
        </v-list-item>
        <v-list-item>
          <v-combobox
            chips
            v-model="filters.country_origin"
            closable-chips
            color="purple-darken-1"
            label="País de Origem"
            multiple
          ></v-combobox>
        </v-list-item>
        <v-list-item>
          <v-select
            :items="modais"
            chips
            color="purple-darken-1"
            v-model="filters.transport_mode"
            closable-chips
            label="Modal"
            multiple
          ></v-select>
        </v-list-item>
        <v-list-item>
          <v-btn block color="purple-darken-1" @click="fetchData()">
            Pesquisar
          </v-btn>
        </v-list-item>
        <v-list-item>
          <v-btn block variant="outlined" color="purple-darken-1">
            Limpar
          </v-btn>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main class="bg-purple-lighten-5">
      <v-container>
        <v-row>
          <v-col>
            <v-sheet class="panel" elevation="1" rounded>
              <div ref="ncmChart" style="height: 100%"></div>
            </v-sheet>
          </v-col>
          <v-col>
            <v-sheet class="panel" elevation="1" rounded>
              <div ref="stateChart" style="height: 100%"></div>
            </v-sheet>
          </v-col>
        </v-row>
        <v-row>
          <v-col>
            <v-sheet class="panel" elevation="1" rounded>
              <div ref="transportModeChart" style="height: 100%"></div>
            </v-sheet>
          </v-col>
          <v-col>
            <v-sheet class="panel" elevation="1" rounded>
              <div ref="originCountryChart" style="height: 100%"></div>
            </v-sheet>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import * as am5 from "@amcharts/amcharts5";
import * as am5xy from "@amcharts/amcharts5/xy";
import am5themes_Animated from "@amcharts/amcharts5/themes/Animated";

export default {
  name: "Dashboard",
  data() {
    return {
      modais: ["AEREA", "MARITIMA", "RODOVIARIA"],
      estados: [
        "Acre",
        "Alagoas",
        "Amapá",
        "Amazonas",
        "Bahia",
        "Ceará",
        "Distrito Federal",
        "Espírito Santo",
        "Goiás",
        "Maranhão",
        "Mato Grosso",
        "Mato Grosso do Sul",
        "Minas Gerais",
        "Pará",
        "Paraíba",
        "Paraná",
        "Pernambuco",
        "Piauí",
        "Rio de Janeiro",
        "Rio Grande do Norte",
        "Rio Grande do Sul",
        "Rondônia",
        "Roraima",
        "Santa Catarina",
        "São Paulo",
        "Sergipe",
        "Tocantins",
      ],
      filters: {
        ncm_code: [],
        country_origin: [],
        importer_state: [],
        transport_mode: [],
      },
    };
  },
  methods: {
    buildNcmChart(chartData) {
      if (this.ncmChartRoot) {
        this.ncmChartRoot.dispose();
      }

      let root = am5.Root.new(this.$refs.ncmChart);

      root.setThemes([am5themes_Animated.new(root)]);

      let chart = root.container.children.push(
        am5xy.XYChart.new(root, {
          panX: true,
          wheelY: "zoomX",
          pinchZoomX: true,
        })
      );

      const chartData2 = chartData.data.slice(0, 10);

      let yAxis = chart.yAxes.push(
        am5xy.ValueAxis.new(root, {
          renderer: am5xy.AxisRendererY.new(root, {}),
        })
      );

      let xAxis = chart.xAxes.push(
        am5xy.CategoryAxis.new(root, {
          renderer: am5xy.AxisRendererX.new(root, {}),
          tooltip: am5.Tooltip.new(root, {}),
          categoryField: "ncm",
        })
      );

      xAxis.data.setAll(chartData2);

      // Create series
      let series = chart.series.push(
        am5xy.ColumnSeries.new(root, {
          name: "Operações",
          xAxis: xAxis,
          yAxis: yAxis,
          valueYField: "operations",
          categoryXField: "ncm",
          tooltip: am5.Tooltip.new(root, {
            labelText: "{valueY}",
          }),
        })
      );
      series.data.setAll(chartData2);

      // series.bullets.push(function () {
      //   return am5.Bullet.new(root, {
      //     locationX: 1,
      //     locationY: 0.5,
      //     sprite: am5.Label.new(root, {
      //       centerX: am5.p100,
      //       centerY: am5.p50,
      //       text: "{ncm}",
      //       fill: am5.color(0xffffff),
      //       populateText: true,
      //     }),
      //   });
      // });

      let legend = chart.children.push(am5.Legend.new(root, {}));
      legend.data.setAll(chart.series.values);

      let cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
      cursor.lineY.set("visible", false);

      this.ncmChartRoot = root;
    },
    buildStateChart(chartData) {
      if (this.stateChartRoot) {
        this.stateChartRoot.dispose();
      }
      let root = am5.Root.new(this.$refs.stateChart);
      root.setThemes([am5themes_Animated.new(root)]);
      let chart = root.container.children.push(
        am5xy.XYChart.new(root, {
          panX: true,
          panY: true,
          wheelX: "panX",
          wheelY: "zoomX",
          layout: root.verticalLayout,
        })
      );

      const chartData2 = chartData.data.slice(0, 10);

      const yAxis = chart.yAxes.push(
        am5xy.CategoryAxis.new(root, {
          categoryField: "importer_state",
          renderer: am5xy.AxisRendererY.new(root, {
            inversed: true,
            cellStartLocation: 0.1,
            cellEndLocation: 0.9,
          }),
        })
      );

      yAxis.data.setAll(chartData2);

      const xAxis = chart.xAxes.push(
        am5xy.ValueAxis.new(root, {
          renderer: am5xy.AxisRendererX.new(root, {
            strokeOpacity: 0.1,
          }),
          min: 0,
        })
      );

      const series = chart.series.push(
        am5xy.ColumnSeries.new(root, {
          name: "Total FOB",
          xAxis: xAxis,
          yAxis: yAxis,
          valueXField: "total_fob_value",
          categoryYField: "importer_state",
          sequencedInterpolation: true,
          tooltip: am5.Tooltip.new(root, {
            pointerOrientation: "horizontal",
            labelText: "[bold]{categoryY}[/]\nUSD {valueX}\n{percentage}%",
          }),
        })
      );
      series.data.setAll(chartData2);

      let legend = chart.children.push(am5.Legend.new(root, {}));
      legend.data.setAll(chart.series.values);

      let cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
      cursor.lineY.set("visible", false);

      this.stateChartRoot = root;
    },
    buildOriginCountryChart(chartData) {
      if (this.originCountryChartRoot) {
        this.originCountryChartRoot.dispose();
      }
      let root = am5.Root.new(this.$refs.originCountryChart);
      root.setThemes([am5themes_Animated.new(root)]);
      let chart = root.container.children.push(
        am5xy.XYChart.new(root, {
          panX: true,
          panY: true,
          wheelX: "panX",
          wheelY: "zoomX",
          layout: root.verticalLayout,
        })
      );

      const chartData2 = chartData.data.slice(0, 10);

      const yAxis = chart.yAxes.push(
        am5xy.CategoryAxis.new(root, {
          categoryField: "country_origin",
          renderer: am5xy.AxisRendererY.new(root, {
            inversed: true,
            cellStartLocation: 0.1,
            cellEndLocation: 0.9,
          }),
        })
      );

      yAxis.data.setAll(chartData2);

      const xAxis = chart.xAxes.push(
        am5xy.ValueAxis.new(root, {
          renderer: am5xy.AxisRendererX.new(root, {
            strokeOpacity: 0.1,
          }),
          min: 0,
        })
      );

      const series = chart.series.push(
        am5xy.ColumnSeries.new(root, {
          name: "Total FOB",
          xAxis: xAxis,
          yAxis: yAxis,
          valueXField: "total_fob_value",
          categoryYField: "country_origin",
          sequencedInterpolation: true,
          tooltip: am5.Tooltip.new(root, {
            pointerOrientation: "horizontal",
            labelText: "[bold]{categoryY}[/]\nUSD {valueX}\n{percentage}%",
          }),
        })
      );
      series.data.setAll(chartData2);

      let legend = chart.children.push(am5.Legend.new(root, {}));
      legend.data.setAll(chart.series.values);

      let cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
      cursor.lineY.set("visible", false);

      this.originCountryChartRoot = root;
    },
    buildTransportModeChart(chartData) {
      if (this.transportModeChartRoot) {
        this.transportModeChartRoot.dispose();
      }
      let root = am5.Root.new(this.$refs.transportModeChart);
      root.setThemes([am5themes_Animated.new(root)]);
      let chart = root.container.children.push(
        am5xy.XYChart.new(root, {
          panX: true,
          panY: true,
          wheelX: "panX",
          wheelY: "zoomX",
          layout: root.verticalLayout,
        })
      );

      const chartData2 = chartData.data.slice(0, 10);

      const yAxis = chart.yAxes.push(
        am5xy.CategoryAxis.new(root, {
          categoryField: "transport_mode",
          renderer: am5xy.AxisRendererY.new(root, {
            inversed: true,
            cellStartLocation: 0.1,
            cellEndLocation: 0.9,
          }),
        })
      );

      yAxis.data.setAll(chartData2);

      const xAxis = chart.xAxes.push(
        am5xy.ValueAxis.new(root, {
          renderer: am5xy.AxisRendererX.new(root, {
            strokeOpacity: 0.1,
          }),
          min: 0,
        })
      );

      const series = chart.series.push(
        am5xy.ColumnSeries.new(root, {
          name: "Total FOB",
          xAxis: xAxis,
          yAxis: yAxis,
          valueXField: "operations",
          categoryYField: "transport_mode",
          sequencedInterpolation: true,
          tooltip: am5.Tooltip.new(root, {
            pointerOrientation: "horizontal",
            labelText: "[bold]{categoryY}[/]\n{percentage}%",
          }),
        })
      );
      series.data.setAll(chartData2);

      let legend = chart.children.push(am5.Legend.new(root, {}));
      legend.data.setAll(chart.series.values);

      let cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
      cursor.lineY.set("visible", false);

      this.transportModeChartRoot = root;
    },
    fetchData() {
      const filters = {
        ncm_code: this.filters.ncm_code,
        country_origin: this.filters.country_origin,
        importer_state: this.filters.importer_state,
        transport_mode: this.filters.transport_mode,
      };

      axios
        .post("http://localhost:8585/api/charts/ncm", { filters })
        .then((response) => {
          this.buildNcmChart(response.data);
        });

      axios
        .post("http://localhost:8585/api/charts/state", { filters })
        .then((response) => {
          this.buildStateChart(response.data);
        });

      axios
        .post("http://localhost:8585/api/charts/origin-country", { filters })
        .then((response) => {
          this.buildOriginCountryChart(response.data);
        });

      axios
        .post("http://localhost:8585/api/charts/transport-mode", { filters })
        .then((response) => {
          this.buildTransportModeChart(response.data);
        });
    },
  },
};
</script>

<style>
.panel {
  width: 100%;
  height: 360px;
  padding: 5px;
}

html {
  overflow: hidden !important;
}
</style>
