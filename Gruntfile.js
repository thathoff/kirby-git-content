module.exports = function ( grunt ) {

    grunt.initConfig( {

        release: {
            options: {
                bump: true, //default: true
                changelog: false, //default: false
                file: 'package.json', //default: package.json
                add: false, //default: true
                commit: false, //default: true
                tag: true, //default: true
                push: false, //default: true
                pushTags: false, //default: true
                npm: false, //default: true
                tagName: 'v<%= version %>', //default: '<%= version %>'
                commitMessage: 'release <%= version %>', //default: 'release <%= version %>'
                tagMessage: 'Version <%= version %>', //default: 'Version <%= version %>',
                beforeBump: [], // optional grunt tasks to run before file versions are bumped
                afterBump: [
                    'conventionalChangelog'
                ], // optional grunt tasks to run after file versions are bumped
                beforeRelease: [], // optional grunt tasks to run after release version is bumped up but before release is packaged
                afterRelease: [], // optional grunt tasks to run after release is packaged
                updateVars: [] // optional grunt config objects to update (this will update/set the version property on the object specified)
            }
        },

        conventionalChangelog: {
            options: {
                changelogOpts: {
                    // conventional-changelog options go here
                    preset: 'angular'
                },
                context: {
                    // context goes here
                },
                gitRawCommitsOpts: {
                    // git-raw-commits options go here
                },
                parserOpts: {
                    // conventional-commits-parser options go here
                },
                writerOpts: {
                    // conventional-changelog-writer options go here
                }
            },
            release: {
                src: 'CHANGELOG.md'
            }
        }

    } );

    grunt.loadNpmTasks( 'grunt-release' );
    grunt.loadNpmTasks( 'grunt-conventional-changelog' );

    grunt.registerTask( 'default', [
        'release'
    ] );

};