import replace from "@rollup/plugin-replace";
import { nodeResolve } from "@rollup/plugin-node-resolve";
const { terser } = require("rollup-plugin-terser");
import vue from "rollup-plugin-vue";
import commonjs from "@rollup/plugin-commonjs";
import json from "@rollup/plugin-json";
import alias from "@rollup/plugin-alias";
import postcss from "rollup-plugin-postcss";

export default [
	{
		input: "src/resources/js/style-guide.js",
		output: {
			file: "src/resources/js/dist/style-guide.min.js",
			format: "iife",
			sourcemap: true,
			plugins: [terser()],
		},
		plugins: [
			alias({
				entries: {
					vue: "vue/dist/vue.runtime.esm-bundler.js",
				},
			}),
			vue({
				preprocessStyles: true,
			}),
			postcss(),
			commonjs(),
			nodeResolve({
				browser: true,
			}),
			json(),
			replace({
				"process.env.NODE_ENV": JSON.stringify("production"),
				preventAssignment: true,
			}),
		],
	},
	{
		input: "src/resources/js/cms-guide.js",
		output: {
			file: "src/resources/js/dist/cms-guide.min.js",
			format: "iife",
			sourcemap: true,
			plugins: [terser()],
		},
		plugins: [
			alias({
				entries: {
					vue: "vue/dist/vue.runtime.esm-bundler.js",
				},
			}),
			vue({
				preprocessStyles: true,
			}),
			postcss(),
			commonjs(),
			nodeResolve({
				browser: true,
			}),
			json(),
			replace({
				"process.env.NODE_ENV": JSON.stringify("production"),
				preventAssignment: true,
			}),
		],
	},
];
