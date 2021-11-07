module.exports = {
    base: '/SymconModule/',
    title: 'JSLive Module',
    description: 'Module zur Visualisierung von Daten in Symcon',
    themeConfig: {
        nav: [
            {text: 'Home', link: '/'},
            {text: 'Config-Demos', link: '/samples/'}
           ],
        sidebar: {
            '/Start/': [
                '',
                'Chart',
                'ChartRadar',
                'ColorPicker',
                'DateTimePicker',
                'DoughnutPie',
                'Gauge',
                'Progressbar'
            ]
        }

    }
}